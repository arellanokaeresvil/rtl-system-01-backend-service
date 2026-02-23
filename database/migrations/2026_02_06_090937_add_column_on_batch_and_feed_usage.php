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
       
        Schema::table('batches', function (Blueprint $table) {
                $table->decimal('daily_feed_per_bird_kg', 6, 4)->nullable()->after('notes');
                $table->boolean('is_auto_feed')->default(false);
        });

        Schema::table('feed_usages', function (Blueprint $table) {
               $table->enum('usage_type', ['manual', 'auto'])->default('manual');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('batches', function (Blueprint $table) {
            $table->dropColumn('daily_feed_per_bird_kg');
            $table->dropColumn('is_auto_feed');
        });

        Schema::table('feed_usages', function (Blueprint $table) {
            $table->dropColumn('usage_type');
        });
    }
};
