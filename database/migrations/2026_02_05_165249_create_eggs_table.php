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
        Schema::create('eggs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            
            $table->uuid('batch_id');
           $table->foreign('batch_id')
                ->references('id')
                ->on('batches')
                ->cascadeOnDelete();

            $table->dateTime('date_collected');

            $table->decimal('weight_grams', 6, 2)->nullable();

            $table->enum('grade', ['S', 'M', 'L', 'XL'])->nullable();

            $table->enum('status', ['good', 'cracked', 'reject'])
                  ->default('good');

            $table->enum('source', ['manual', 'esp32'])
                  ->default('manual');

            $table->string('device_id')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('eggs');
    }
};
