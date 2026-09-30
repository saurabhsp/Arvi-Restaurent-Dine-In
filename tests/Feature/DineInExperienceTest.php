<?php
namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DineInExperienceTest extends TestCase {
    use RefreshDatabase;

    public function test_mobile_navigation_and_marathi_menu_are_rendered(): void {
        $category=Category::create(['name'=>'Starters','name_mr'=>'सुरुवातीचे पदार्थ']);
        Product::create(['category_id'=>$category->id,'name'=>'Paneer','name_mr'=>'पनीर','price'=>150]);

        $this->withSession(['locale'=>'mr','order_details'=>['name'=>'Guest','phone'=>'9876543210','payment_method'=>'cash'],'customer_phone'=>'9876543210'])
            ->get(route('home'))
            ->assertOk()
            ->assertSee('lang="mr"', false)
            ->assertSee('mobile-nav')
            ->assertSee('product-row')
            ->assertSee('सुरुवातीचे पदार्थ')
            ->assertSee('पनीर')
            ->assertSee('ऑर्डर इतिहास');
    }

    public function test_history_shows_only_orders_for_the_entered_mobile_number(): void {
        Order::create(['customer_name'=>'Customer','customer_phone'=>'9876543210','payment_method'=>'cash','payment_status'=>'unpaid','total'=>125]);
        Order::create(['customer_name'=>'Someone else','customer_phone'=>'9000000000','payment_method'=>'cash','payment_status'=>'unpaid','total'=>999]);

        $this->post(route('history.open'), ['phone'=>'9876543210'])->assertRedirect(route('history'));
        $this->get(route('history'))->assertOk()->assertSee('₹125.00')->assertDontSee('₹999.00');

        $this->post(route('history.open'), ['phone'=>'8888888888'])->assertRedirect(route('history'));
        $this->get(route('history'))->assertOk()->assertSee('No history found');

        $this->withSession(['admin_user_id'=>1])->get(route('history'))->assertForbidden();
    }
}
