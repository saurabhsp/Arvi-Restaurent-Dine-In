<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::table('categories', function (Blueprint $table) {
            $table->string('name_mr')->nullable();
            $table->text('description_mr')->nullable();
        });
        Schema::table('products', function (Blueprint $table) {
            $table->string('name_mr')->nullable();
            $table->text('description_mr')->nullable();
        });
    }
    public function down(): void {
        Schema::table('products', fn (Blueprint $table) => $table->dropColumn(['name_mr','description_mr']));
        Schema::table('categories', fn (Blueprint $table) => $table->dropColumn(['name_mr','description_mr']));
    }
};
