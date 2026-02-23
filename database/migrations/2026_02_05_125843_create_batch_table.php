<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('batches', function (Blueprint $table) {
            $table->uuid('id')->primary();

            // Basic info
            $table->string('batch_code')->unique();
            $table->string('supplier_name')->nullable();

            // Dates & age
            $table->date('date_received');
            $table->unsignedTinyInteger('initial_age_weeks');

            // Quantity
            $table->unsignedInteger('initial_quantity');
            $table->unsignedInteger('current_quantity');

            // Cost
            $table->decimal('cost_per_head', 10, 2)->nullable();

            // Status
            $table->enum('status', [
                'active',
                'peak',
                'sold',
                'culled'
            ])->default('active');

            // Notes
            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('batches');
    }
};
