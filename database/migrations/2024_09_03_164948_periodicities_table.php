<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        Schema::create('periodicities', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->unsignedBigInteger('duration'); // Duration in seconds
        });

        // Insert default periodicities
        DB::table('periodicities')->insert([
            ['name' => 'dnenva', 'duration' => 86400], // 24 hours in seconds
            ['name' => 'nedeljna', 'duration' => 604800], // 7 days in seconds
            ['name' => 'mesečna', 'duration' => 2592000], // 30 days in seconds (approximate)
        ]);
    }

    public function down()
    {
        Schema::dropIfExists('periodicities');
    }
};