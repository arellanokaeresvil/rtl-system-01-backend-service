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
         Schema::table('eggs', function (Blueprint $table) {
              $table->unsignedInteger('total')->nullable()->after('status');
              $table->enum('unit', ['piece', 'tray', 'custom'])->default('piece')->after('total');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('eggs', function (Blueprint $table) {
            $table->dropColumn('total');
            $table->dropColumn('unit');
        });
    }
};
