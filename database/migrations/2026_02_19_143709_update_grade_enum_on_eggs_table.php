<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
public function up()
{
    DB::statement("
        ALTER TABLE eggs 
        MODIFY grade ENUM('P', 'XS', 'S', 'M', 'L', 'XL', 'J') NULL
    ");
}

public function down()
{
    DB::statement("
        ALTER TABLE eggs 
        MODIFY grade ENUM('S', 'M', 'L', 'XL') NULL
    ");
}
};
