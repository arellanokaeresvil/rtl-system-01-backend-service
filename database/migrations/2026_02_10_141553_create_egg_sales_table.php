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
       Schema::create('egg_sales', function (Blueprint $table) {
        $table->uuid('id')->primary();
        $table->uuid('batch_id')->nullable();
        $table->date('sold_at');
        $table->string('sold_to')->nullable();

        $table->integer('quantity');
        $table->enum('unit', ['piece', 'tray'])->default('piece');
        $table->decimal('price_per_unit', 10, 2);
        $table->decimal('total_amount', 12, 2);

        $table->text('notes')->nullable();

        $table->timestamps();
        $table->softDeletes();

        $table->foreign('batch_id')->references('id')->on('batches')->nullOnDelete();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('egg_sales');
    }
};
