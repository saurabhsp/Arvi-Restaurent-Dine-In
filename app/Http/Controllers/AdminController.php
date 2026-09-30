<?php
namespace App\Http\Controllers;
use App\Models\Category;
use App\Models\AdminUser;
use App\Models\Order;
use App\Models\Product;
use App\Models\PaymentQrCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class AdminController extends Controller {
    public function loginForm() { return view('admin.login'); }
    public function login(Request $r) {
        $credentials=$r->validate(['username'=>'required|string','password'=>'required|string']);
        $admin=AdminUser::where('username',$credentials['username'])->first();
        if ($admin && Hash::check($credentials['password'],$admin->password)) {
            $r->session()->regenerate();
            $r->session()->put(['admin_user_id'=>$admin->id,'admin_name'=>$admin->name]);
            return redirect()->route('admin.dashboard');
        }
        return back()->withErrors(['username'=>'Invalid admin credentials.'])->onlyInput('username');
    }
    public function createForm() { abort_if(AdminUser::exists(), 404); return view('admin.create'); }
    public function storeAdmin(Request $r) {
        abort_if(AdminUser::exists(), 404);
        $data=$r->validate([
            'name'=>'required|string|min:2|max:100',
            'username'=>'required|string|min:3|max:50|alpha_dash|unique:admin_users,username',
            'password'=>'required|string|min:8|max:255',
            'mobile'=>'required|string|min:8|max:20',
        ]);
        AdminUser::create(['name'=>trim($data['name']),'username'=>strtolower($data['username']),'password'=>Hash::make($data['password']),'mobile'=>trim($data['mobile'])]);
        return redirect()->route('admin.login')->with('success','Admin account created. Please log in.');
    }
    public function logout(Request $r) { $r->session()->invalidate(); $r->session()->regenerateToken(); return redirect()->route('admin.login'); }
    public function dashboard(Request $r) {
        $date = $this->dashboardDate($r);
        return view('admin.dashboard', array_merge($this->dashboardPayload($date), ['categories'=>Category::count(),'products'=>Product::count()]));
    }
    public function dashboardData(Request $r) { return response()->json($this->dashboardPayload($this->dashboardDate($r))); }
    public function invoice(Order $order) { return view('admin.invoice', ['order'=>$order->load('items'),'paymentQrCode'=>PaymentQrCode::first()]); }
    public function categories() { return view('admin.categories',['categories'=>Category::withCount('products')->orderBy('sort_order')->get()]); }
    public function saveCategory(Request $r) {
        $d=$r->validate(['id'=>'nullable|exists:categories,id','name'=>'required|string|max:100','name_mr'=>'nullable|string|max:100','description'=>'nullable|string|max:1000','description_mr'=>'nullable|string|max:1000','sort_order'=>'nullable|integer|min:0']);
        $category=isset($d['id'])?Category::findOrFail($d['id']):new Category;
        $category->fill(['name'=>$d['name'],'name_mr'=>$d['name_mr']??null,'description'=>$d['description']??null,'description_mr'=>$d['description_mr']??null,'sort_order'=>$d['sort_order']??0,'is_active'=>$r->boolean('is_active')])->save();
        return back()->with('success','Category saved.');
    }
    public function deleteCategory(Category $category) { if($category->products()->exists()) return back()->withErrors(['category'=>'Remove or move its products first.']); $category->delete(); return back()->with('success','Category deleted.'); }
    public function products() { return view('admin.products',['products'=>Product::with('category')->orderBy('sort_order')->get(),'categories'=>Category::orderBy('name')->get()]); }
    public function upiQr() { return view('admin.upi-qr', ['paymentQrCode'=>PaymentQrCode::first()]); }
    public function uploadUpiQr(Request $r) {
        if (PaymentQrCode::exists()) return back()->withErrors(['qr'=>'Delete the existing QR code before uploading another one.']);
        $data=$r->validate([
            'qr_image'=>'required|image|mimes:png,jpg,jpeg,webp|max:4096',
            'upi_id'=>['required','string','max:100','regex:/^[A-Za-z0-9._-]+@[A-Za-z0-9.-]+$/'],
            'payee_name'=>'required|string|max:100',
        ]);
        $path=$data['qr_image']->store('payment-qr','public');
        try {
            PaymentQrCode::create(['image_path'=>$path,'upi_id'=>$data['upi_id'],'payee_name'=>trim($data['payee_name'])]);
        } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
            Storage::disk('public')->delete($path);
            return back()->withErrors(['qr'=>'A QR code is already active. Delete it before uploading another one.']);
        }
        return back()->with('success','UPI QR code uploaded.');
    }
    public function deleteUpiQr(PaymentQrCode $paymentQrCode) {
        Storage::disk('public')->delete($paymentQrCode->image_path);
        $paymentQrCode->delete();
        return back()->with('success','UPI QR code deleted. You can upload a new one.');
    }
    public function adminOrderForm() {
        $categories=Category::where('is_active',true)->with(['products'=>fn($query)=>$query->where('is_available',true)->orderBy('sort_order')->orderBy('name')])->orderBy('sort_order')->get();
        $customerNames=Order::query()->select('customer_name')->whereNotNull('customer_name')->distinct()->orderBy('customer_name')->pluck('customer_name');
        return view('admin.order-create', compact('categories','customerNames'));
    }
    public function storeAdminOrder(Request $r) {
        $data=$r->validate(['name'=>'required|string|min:2|max:100','phone'=>'nullable|string|max:20','payment_method'=>'required|in:cash,upi,card','payment_status'=>'required|in:paid,unpaid,credit','items'=>'required|array|min:1','items.*.id'=>'required|integer|distinct','items.*.quantity'=>'required|integer|min:1|max:50']);
        $ids=collect($data['items'])->pluck('id');
        $products=Product::whereIn('id',$ids)->where('is_available',true)->whereHas('category',fn($query)=>$query->where('is_active',true))->get()->keyBy('id');
        if ($products->count()!==$ids->count()) throw ValidationException::withMessages(['items'=>'A selected item is no longer available. Please refresh the menu.']);
        $order=DB::transaction(function() use($data,$products) {
            $total=0; $rows=[];
            foreach($data['items'] as $item) { $product=$products[$item['id']]; $line=(int)round((float)$product->price*100)*$item['quantity']; $total+=$line; $rows[]=['product_id'=>$product->id,'product_name'=>$product->name,'unit_price'=>$product->price,'quantity'=>$item['quantity'],'line_total'=>$line/100]; }
            $order=Order::create(['customer_name'=>trim($data['name']),'customer_phone'=>filled($data['phone'] ?? null)?trim($data['phone']):null,'payment_method'=>$data['payment_method'],'payment_status'=>$data['payment_status'],'total'=>$total/100]);
            $order->items()->createMany($rows);
            return $order;
        });
        return redirect()->route('admin.orders.invoice',$order)->with('success','Order '.$order->order_number.' created.');
    }
    public function saveProduct(Request $r) {
        $d=$r->validate(['id'=>'nullable|exists:products,id','category_id'=>'required|exists:categories,id','name'=>'required|string|max:150','name_mr'=>'nullable|string|max:150','description'=>'nullable|string|max:2000','description_mr'=>'nullable|string|max:2000','price'=>'required|numeric|min:0|max:99999999','image'=>'nullable|image|mimes:jpeg,png,webp|max:4096','sort_order'=>'nullable|integer|min:0']);
        $p=isset($d['id'])?Product::findOrFail($d['id']):new Product;
        if($r->hasFile('image')) { if($p->image_path) Storage::disk('public')->delete($p->image_path); $p->image_path=$r->file('image')->store('products','public'); }
        $p->fill(['category_id'=>$d['category_id'],'name'=>$d['name'],'name_mr'=>$d['name_mr']??null,'description'=>$d['description']??null,'description_mr'=>$d['description_mr']??null,'price'=>$d['price'],'sort_order'=>$d['sort_order']??0,'is_available'=>$r->boolean('is_available')])->save();
        return back()->with('success','Product saved.');
    }
    public function deleteProduct(Product $product) { if($product->image_path) Storage::disk('public')->delete($product->image_path); $product->delete(); return back()->with('success','Product deleted.'); }
    public function orders(Request $r) { return view('admin.orders', ['date'=>$this->dashboardDate($r)->toDateString()]); }
    public function updateOrder(Request $r,Order $order) {
        $data=$r->validate(['status'=>['nullable',Rule::in(['new','preparing','served','cancelled'])],'payment_status'=>['nullable',Rule::in(['paid','unpaid','credit'])],'payment_method'=>['nullable',Rule::in(['cash','upi','card'])]]);
        $order->update(array_filter($data, fn($value)=>$value !== null));
        if ($r->expectsJson()) return response()->json(['ok'=>true]);
        return back()->with('success','Order updated.');
    }
    private function dashboardDate(Request $r): Carbon {
        $value=$r->validate(['date'=>'nullable|date_format:Y-m-d'])['date'] ?? now()->toDateString();
        return Carbon::createFromFormat('Y-m-d',$value)->startOfDay();
    }
    private function dashboardPayload(Carbon $date): array {
        $orders=Order::with('items')->whereDate('created_at',$date)->latest()->get();
        return [
            'date'=>$date->toDateString(),
            'orders'=>$orders,
            'stats'=>[
                'totalOrders'=>$orders->count(),
                'totalSales'=>(float)$orders->where('status','!=','cancelled')->sum('total'),
                'newOrders'=>$orders->where('status','new')->count(),
                'completedOrders'=>$orders->where('status','served')->count(),
            ],
        ];
    }
}
