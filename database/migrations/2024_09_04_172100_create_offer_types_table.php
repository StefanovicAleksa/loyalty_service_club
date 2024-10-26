<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\OfferType;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('offer_types', function (Blueprint $table) {
            $table->tinyIncrements('id');
            $table->string('name')->unique();
        });

        // Insert predefined offer types
        $types = OfferType::getTypes();
        foreach ($types as $type) {
            DB::table('offer_types')->insert(['name' => $type]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('offer_types');
    }
};