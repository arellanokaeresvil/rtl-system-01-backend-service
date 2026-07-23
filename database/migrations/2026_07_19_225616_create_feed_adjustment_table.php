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
        Schema::create('feed_adjustment', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('feed_id'); 
            $table->decimal('quantity_kg', 8, 2);
            $table->decimal('cost', 10, 2);
            $table->string('reason');
            $table->string('remarks')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('feed_id')->references('id')->on('feeds')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feed_adjustment');
    }
};
