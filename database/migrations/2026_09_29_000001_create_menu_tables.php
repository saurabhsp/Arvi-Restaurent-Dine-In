<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('categories', function(Blueprint $t) { $t->id(); $t->string('name'); $t->text('description')->nullable(); $t->unsignedInteger('sort_order')->default(0); $t->boolean('is_active')->default(true); $t->timestamps(); });
        Schema::create('products', function(Blueprint $t) { $t->id(); $t->foreignId('category_id')->constrained()->restrictOnDelete(); $t->string('name'); $t->text('description')->nullable(); $t->decimal('price',10,2); $t->string('image_path')->nullable(); $t->boolean('is_available')->default(true); $t->unsignedInteger('sort_order')->default(0); $t->timestamps(); });
        Schema::create('orders', function(Blueprint $t) { $t->id(); $t->string('customer_name'); $t->string('status')->default('new'); $t->decimal('total',10,2); $t->timestamps(); });
        Schema::create('order_items', function(Blueprint $t) { $t->id(); $t->foreignId('order_id')->constrained()->cascadeOnDelete(); $t->foreignId('product_id')->nullable()->constrained()->nullOnDelete(); $t->string('product_name'); $t->decimal('unit_price',10,2); $t->unsignedInteger('quantity'); $t->decimal('line_total',10,2); $t->timestamps(); });
    }
    public function down(): void { Schema::dropIfExists('order_items'); Schema::dropIfExists('orders'); Schema::dropIfExists('products'); Schema::dropIfExists('categories'); }
};
