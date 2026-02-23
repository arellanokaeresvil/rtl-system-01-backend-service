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
        Schema::create('bird_sales', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('batch_id');
            $table->date('sold_at');
            $table->string('sold_to')->nullable();
            $table->integer('count');
            $table->decimal('price_per_bird', 10, 2);
            $table->decimal('total_amount', 12, 2);
            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();
            $table->foreign('batch_id')->references('id')->on('batches');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bird_sales');
    }
};
