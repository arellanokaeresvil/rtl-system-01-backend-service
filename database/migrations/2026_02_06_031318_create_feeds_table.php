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
        Schema::create('feeds', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('feed_code')->unique();
            $table->string('name');
            $table->string('type');
            $table->date('date_manufactured');
            $table->decimal('quantity_kg', 10, 2);
            $table->decimal('remaining_kg', 10, 2)->nullable();
            $table->decimal('cost_per_kg', 10, 2);
            $table->string('supplier')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('feed_usages', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->uuid('feed_id'); 
            $table->uuid('batch_id');

            $table->dateTime('used_at');
            $table->decimal('quantity_kg', 8, 2);

            $table->enum('source', ['manual', 'auto'])->default('manual');
            $table->string('remarks')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('feed_id')->references('id')->on('feeds')->cascadeOnDelete();
            $table->foreign('batch_id')->references('id')->on('batches')->cascadeOnDelete();
            $table->index(['batch_id', 'used_at']);
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feeds');
        Schema::dropIfExists('feed_usages');
    }
};
