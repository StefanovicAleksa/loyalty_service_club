<?php

namespace Tests\Feature\Database;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Artisan;
use App\Models\Customer;
use App\Models\User;
use App\Models\Offer;
use App\Models\OfferType;
use App\Models\OfferChoice;
use App\Models\QrCode;
use App\Models\Message;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_seeder(): void
    {
        // Run the DatabaseSeeder
        Artisan::call('db:seed');

        // Test that data has been seeded
        $this->assertDatabaseCount('customers', 50);
        $this->assertDatabaseCount('users', 50);
        $this->assertDatabaseCount('offer_types', 4);
        $this->assertDatabaseHas('offer_types', ['name' => 'jednokratna']);
        $this->assertDatabaseHas('offer_types', ['name' => 'stalna']);
        $this->assertDatabaseHas('offer_types', ['name' => 'periodična']);
        $this->assertDatabaseHas('offer_types', ['name' => 'periodična-specijalna']);

        $this->assertDatabaseCount('offers', 20);
        $this->assertDatabaseCount('offer_validities', 20);
        
        // Check that each offer has at least one choice
        $offers = Offer::all();
        foreach ($offers as $offer) {
            $this->assertGreaterThan(0, $offer->choices()->count(), "Offer {$offer->id} has no choices");
        }

        $this->assertDatabaseCount('qr_codes', 100);
        $this->assertDatabaseCount('messages', 50);

        // Test relationships
        $user = User::first();
        $this->assertInstanceOf(Customer::class, $user->customer);

        $offer = Offer::first();
        $this->assertInstanceOf(OfferType::class, $offer->offerType);
        $this->assertInstanceOf(OfferChoice::class, $offer->choices->first());

        $qrCode = QrCode::first();
        $this->assertInstanceOf(Customer::class, $qrCode->customer);
        $this->assertInstanceOf(OfferChoice::class, $qrCode->offerChoice);

        // Test phone number format and uniqueness
        $customers = Customer::all();
        $phoneNumbers = $customers->pluck('phone')->toArray();
        $this->assertCount(50, array_unique($phoneNumbers), 'Phone numbers are not unique');
        foreach ($phoneNumbers as $phone) {
            $this->assertMatchesRegularExpression('/^\+[1-9]\d{9}$/', $phone, "Phone number {$phone} does not match the expected format");
        }
    }
}