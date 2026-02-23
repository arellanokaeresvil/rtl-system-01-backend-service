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
        Schema::create('expenses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('batch_id')->nullable(); 
            $table->uuid('expense_category_id');
            $table->date('expense_date');
            $table->decimal('amount', 10, 2);
            $table->string('reference_no')->nullable(); 
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('batch_id')->references('id')->on('batches');
            $table->foreign('expense_category_id')->references('id')->on('expense_categories');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
