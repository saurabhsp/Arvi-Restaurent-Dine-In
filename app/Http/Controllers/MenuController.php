<?php
namespace App\Http\Controllers;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\PaymentQrCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MenuController extends Controller {
    public function index(Request $request) {
        $categories = Category::where('is_active',true)->with(['products'=>fn($q)=>$q->where('is_available',true)->orderBy('sort_order')->orderBy('name')])->orderBy('sort_order')->orderBy('name')->get();
        $orderDetails = $request->session()->get('order_details');
        $paymentQrCode = PaymentQrCode::first();
        return view('menu', compact('categories', 'orderDetails', 'paymentQrCode'));
    }
    public function start(Request $request) {
        $data=$request->validate([
            'name'=>'required|string|min:2|max:100',
            'phone'=>'nullable|string|max:20',
            'payment_method'=>'required|in:cash,upi,card',
        ]);
        return redirect()->route('home')->with('order_details', [
            'name'=>trim($data['name']),
            'phone'=>filled($data['phone'] ?? null) ? trim($data['phone']) : null,
            'payment_method'=>$data['payment_method'],
        ]);
    }
    public function order(Request $request) {
        $data=$request->validate(['name'=>'required|string|min:2|max:100','phone'=>'nullable|string|max:20','payment_method'=>'required|in:cash,upi,card','items'=>'required|array|min:1','items.*.id'=>'required|integer|distinct','items.*.quantity'=>'required|integer|min:1|max:50']);
        $ids=collect($data['items'])->pluck('id');
        $products=Product::whereIn('id',$ids)->where('is_available',true)->whereHas('category',fn($q)=>$q->where('is_active',true))->get()->keyBy('id');
        if ($products->count()!==$ids->count()) throw ValidationException::withMessages(['items'=>'A selected item is no longer available. Please refresh the menu.']);
        $order=DB::transaction(function() use($data,$products,$request) {
            $total=0; $rows=[];
            foreach($data['items'] as $item) { $p=$products[$item['id']]; $line=(int)round((float)$p->price*100)*$item['quantity']; $total+=$line; $rows[]=['product_id'=>$p->id,'product_name'=>$p->name,'unit_price'=>$p->price,'quantity'=>$item['quantity'],'line_total'=>$line/100]; }
            $order=Order::create([
                'customer_name'=>trim($data['name']),
                'customer_phone'=>filled($data['phone'] ?? null) ? trim($data['phone']) : null,
                'payment_method'=>$data['payment_method'],
                'payment_status'=>'unpaid',
                'total'=>$total/100,
            ]);
            $order->items()->createMany($rows); return $order;
        });
        $request->session()->put('customer_order_id', $order->id);
        return redirect()->route('orders.confirmation');
    }
    public function confirmation(Request $request) {
        $orderId=$request->session()->get('customer_order_id');
        abort_unless($orderId, 404);
        $order=Order::with('items')->findOrFail($orderId);
        $paymentQrCode=$order->payment_method==='upi' ? PaymentQrCode::first() : null;
        $upiUrl=null;
        if ($paymentQrCode?->upi_id) {
            $upiUrl='upi://pay?'.http_build_query([
                'pa'=>$paymentQrCode->upi_id,
                'pn'=>$paymentQrCode->payee_name ?: 'Restaurant',
                'am'=>number_format((float)$order->total, 2, '.', ''),
                'cu'=>'INR',
                'tn'=>$order->order_number,
            ], '', '&', PHP_QUERY_RFC3986);
        }
        return view('order-confirmation', compact('order','paymentQrCode','upiUrl'));
    }
}
