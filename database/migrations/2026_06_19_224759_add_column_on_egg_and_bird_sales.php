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
              $table->enum('payment_status', ['paid', 'unpaid', 'partial'])->default('paid')->after('reference_no');
              $table->decimal('partial_amount', 12, 2)->nullable()->after('payment_status');
              $table->decimal('balance', 12, 2)->nullable()->after('partial_amount');
        });
         Schema::table('bird_sales', function (Blueprint $table) {
              $table->enum('payment_status', ['paid', 'unpaid', 'partial'])->default('paid')->after('reference_no');
              $table->decimal('partial_amount', 12, 2)->nullable()->after('payment_status');
              $table->decimal('balance', 12, 2)->nullable()->after('partial_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
             Schema::table('egg_sales', function (Blueprint $table) {
            $table->dropColumn('payment_status');
            $table->dropColumn('partial_amount');
            $table->dropColumn('balance');
        });
        Schema::table('bird_sales', function (Blueprint $table) {
            $table->dropColumn('payment_status');
            $table->dropColumn('partial_amount');
            $table->dropColumn('balance');
        });
    }
};
