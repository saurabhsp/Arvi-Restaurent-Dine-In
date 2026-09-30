<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\MenuController;
use Illuminate\Support\Facades\Route;

Route::get('/', [MenuController::class, 'index'])->name('home');
Route::post('/locale', function (\Illuminate\Http\Request $request) {
    $data = $request->validate(['locale' => 'required|in:en,mr']);
    $request->session()->put('locale', $data['locale']);
    return back();
})->name('locale.update');
Route::post('/start', [MenuController::class, 'start'])->name('start');
Route::post('/history', [MenuController::class, 'openHistory'])->middleware('throttle:10,1')->name('history.open');
Route::get('/history', [MenuController::class, 'history'])->middleware('throttle:30,1')->name('history');
Route::post('/orders', [MenuController::class, 'order'])->name('orders.store');
Route::get('/order/confirmation', [MenuController::class, 'confirmation'])->name('orders.confirmation');
Route::get('/admin/login', [AdminController::class, 'loginForm'])->name('admin.login');
Route::get('/login', fn() => redirect()->route('admin.login'))->name('login');
Route::post('/admin/login', [AdminController::class, 'login'])->name('admin.login.submit');
Route::get('/admin/create.php', [AdminController::class, 'createForm'])->name('admin.create');
Route::post('/admin/create.php', [AdminController::class, 'storeAdmin'])->name('admin.create.store');
Route::post('/admin/logout', [AdminController::class, 'logout'])->name('admin.logout');
Route::middleware([\App\Http\Middleware\EnsureAdmin::class])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/dashboard-data', [AdminController::class, 'dashboardData'])->name('dashboard.data');
    Route::get('/categories', [AdminController::class, 'categories'])->name('categories');
    Route::post('/categories', [AdminController::class, 'saveCategory'])->name('categories.save');
    Route::delete('/categories/{category}', [AdminController::class, 'deleteCategory'])->name('categories.delete');
    Route::get('/products', [AdminController::class, 'products'])->name('products');
    Route::post('/products', [AdminController::class, 'saveProduct'])->name('products.save');
    Route::delete('/products/{product}', [AdminController::class, 'deleteProduct'])->name('products.delete');
    Route::get('/upi-qr', [AdminController::class, 'upiQr'])->name('upi-qr');
    Route::post('/upi-qr', [AdminController::class, 'uploadUpiQr'])->name('upi-qr.upload');
    Route::delete('/upi-qr/{paymentQrCode}', [AdminController::class, 'deleteUpiQr'])->name('upi-qr.delete');
    Route::get('/orders', [AdminController::class, 'orders'])->name('orders');
    Route::get('/orders/create', [AdminController::class, 'adminOrderForm'])->name('orders.create');
    Route::post('/orders/create', [AdminController::class, 'storeAdminOrder'])->name('orders.store');
    Route::patch('/orders/{order}', [AdminController::class, 'updateOrder'])->name('orders.update');
    Route::get('/orders/{order}/invoice', [AdminController::class, 'invoice'])->name('orders.invoice');
});
