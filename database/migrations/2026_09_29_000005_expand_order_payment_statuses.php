<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        DB::table('orders')->where('payment_status', 'pending')->update(['payment_status'=>'unpaid']);
        DB::statement("ALTER TABLE orders MODIFY payment_status VARCHAR(20) NOT NULL DEFAULT 'unpaid'");
    }
    public function down(): void {
        DB::table('orders')->where('payment_status', 'credit')->update(['payment_status'=>'unpaid']);
    }
};
