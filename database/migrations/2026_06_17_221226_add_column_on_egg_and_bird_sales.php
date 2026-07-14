<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
         Schema::table('egg_sales', function (Blueprint $table) {
              $table->enum('mode_of_payment', ['cash', 'e_payment'])->default('cash')->after('total_amount');
              $table->string('reference_no')->nullable()->after('mode_of_payment');
              $table->boolean('is_paid')->default(true)->after('reference_no');
        });
         Schema::table('bird_sales', function (Blueprint $table) {
              $table->enum('mode_of_payment', ['cash', 'e_payment'])->default('cash')->after('total_amount');
              $table->string('reference_no')->nullable()->after('mode_of_payment');
              $table->boolean('is_paid')->default(true)->after('reference_no');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('egg_sales', function (Blueprint $table) {
            $table->dropColumn('mode_of_payment');
            $table->dropColumn('reference_no');
            $table->dropColumn('is_paid');
        });
        Schema::table('bird_sales', function (Blueprint $table) {
            $table->dropColumn('mode_of_payment');
            $table->dropColumn('reference_no');
            $table->dropColumn('is_paid');
        });
    }
};
