<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('batches', function (Blueprint $table) {
            
            $table->unsignedInteger('daily_feed_per_bird_kg_new')->nullable();
        });

        
        DB::table('batches')->update([
            'daily_feed_per_bird_kg_new' => DB::raw('FLOOR(daily_feed_per_bird_kg)')
        ]);

        Schema::table('batches', function (Blueprint $table) {
            
            $table->dropColumn('daily_feed_per_bird_kg');
            $table->renameColumn('daily_feed_per_bird_kg_new', 'daily_feed_per_bird_kg');
        });
    }

    public function down(): void
    {
        Schema::table('batches', function (Blueprint $table) {
            $table->decimal('daily_feed_per_bird_kg', 6, 4)->nullable();
        });

        DB::table('batches')->update([
            'daily_feed_per_bird_kg' => DB::raw('daily_feed_per_bird_kg')
        ]);
    }
};
