<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('periodical_offer_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('offer_id')->constrained()->onDelete('cascade');
            $table->foreignId('periodicity_id')->constrained();
            $table->unsignedTinyInteger('day_of_week')->nullable();
            $table->time('time_of_day_start')->nullable();
            $table->time('time_of_day_end')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('periodical_offer_details');
    }
};