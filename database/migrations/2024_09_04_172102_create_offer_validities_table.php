<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('offer_validities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('offer_id')->constrained()->onDelete('cascade');
            $table->timestamp('valid_from');
            $table->timestamp('valid_until');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('offer_validities');
    }
};