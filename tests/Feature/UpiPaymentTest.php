<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\PaymentQrCode;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UpiPaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_one_qr_can_be_uploaded_until_it_is_deleted(): void
    {
        Storage::fake('public');
        $this->withSession(['admin_user_id' => 1]);
        $this->post(route('admin.upi-qr.upload'), [
            'qr_image' => UploadedFile::fake()->image('first.png'),
            'upi_id' => 'restaurant@bank',
            'payee_name' => 'Dine In',
        ])->assertSessionHasNoErrors();

        $qr = PaymentQrCode::firstOrFail();
        Storage::disk('public')->assertExists($qr->image_path);

        $this->post(route('admin.upi-qr.upload'), [
            'qr_image' => UploadedFile::fake()->image('second.png'),
            'upi_id' => 'other@bank',
            'payee_name' => 'Other',
        ])->assertSessionHasErrors('qr');
        $this->assertDatabaseCount('payment_qr_codes', 1);

        $this->delete(route('admin.upi-qr.delete', $qr))->assertSessionHasNoErrors();
        Storage::disk('public')->assertMissing($qr->image_path);
        $this->post(route('admin.upi-qr.upload'), [
            'qr_image' => UploadedFile::fake()->image('new.png'),
            'upi_id' => 'new@bank',
            'payee_name' => 'New Restaurant',
        ])->assertSessionHasNoErrors();
        $this->assertDatabaseCount('payment_qr_codes', 1);
    }

    public function test_upi_order_is_unpaid_and_shows_payment_options_and_invoice_qr(): void
    {
        $category = Category::create(['name' => 'Meals']);
        $product = Product::create(['category_id' => $category->id, 'name' => 'Thali', 'price' => 250]);
        PaymentQrCode::create(['image_path' => 'payment-qr/restaurant.png', 'upi_id' => 'restaurant@bank', 'payee_name' => 'Dine In']);

        $this->post(route('orders.store'), [
            'name' => 'Guest', 'payment_method' => 'upi',
            'items' => [['id' => $product->id, 'quantity' => 2]],
        ])->assertRedirect(route('orders.confirmation'));

        $order = Order::firstOrFail();
        $this->assertSame('unpaid', $order->payment_status);
        $this->assertEquals(500, $order->total);
        $this->get(route('orders.confirmation'))
            ->assertOk()
            ->assertSee('Open GPay, PhonePe, or another UPI app')
            ->assertSee('upi://pay?pa=restaurant%40bank', false)
            ->assertSee('am=500.00', false);

        $this->withSession(['admin_user_id' => 1])
            ->get(route('admin.orders.invoice', $order))
            ->assertOk()
            ->assertSee('Scan to pay with UPI')
            ->assertSee('payment-qr/restaurant.png');
    }
}
