<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration {
    public function up(): void {
        DB::table('orders')->where('payment_status', 'pending')->update(['payment_status'=>'unpaid']);
        Schema::table('orders', function (Blueprint $table) {
            $table->string('payment_status', 20)->default('unpaid')->change();
        });
    }
    public function down(): void {
        DB::table('orders')->where('payment_status', 'credit')->update(['payment_status'=>'unpaid']);
    }
};
