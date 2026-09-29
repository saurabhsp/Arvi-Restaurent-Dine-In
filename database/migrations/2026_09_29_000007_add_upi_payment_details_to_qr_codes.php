<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('payment_qr_codes', function (Blueprint $table) {
            $table->string('upi_id', 100)->nullable();
            $table->string('payee_name', 100)->nullable();
            $table->unsignedTinyInteger('singleton_key')->default(1)->unique();
        });
    }

    public function down(): void {
        Schema::table('payment_qr_codes', function (Blueprint $table) {
            $table->dropUnique(['singleton_key']);
            $table->dropColumn(['upi_id', 'payee_name', 'singleton_key']);
        });
    }
};
