<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('offer_choices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('offer_id')->constrained()->onDelete('cascade');
            $table->string('name', 100);
            $table->text('description');
            $table->string('image_path')->nullable();
            $table->string('image_filename')->nullable();
            $table->unsignedInteger('image_size')->nullable();
            $table->timestamp('image_uploaded_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('offer_choices');
    }
};