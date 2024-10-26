<?php

namespace Tests\Unit;

use App\Models\Customer;
use App\Models\Offer;
use App\Models\OfferChoice;
use App\Models\OfferType;
use App\Models\OfferValidity;
use App\Models\PeriodicalOfferDetail;
use App\Models\QrCode;
use App\Services\OfferService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;

class OfferServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow('2023-05-01 12:00:00'); // Monday at noon

        // Ensure offer types exist in the test database
        $types = OfferType::getTypes();
        foreach ($types as $type) {
            OfferType::firstOrCreate(['name' => $type]);
        }
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_store_offer(): void
    {
        $offerType = OfferType::where('name', 'jednokratna')->first();
        $offerData = [
            'name' => 'Test Offer',
            'description' => 'Test Description',
            'offer_type_id' => $offerType->id,
            'validity' => [
                'valid_from' => now()->toDateTimeString(),
                'valid_until' => now()->addDays(30)->toDateTimeString(),
            ],
        ];

        try {
            $offer = OfferService::storeOffer($offerData);

            $this->assertInstanceOf(Offer::class, $offer);
            $this->assertEquals('Test Offer', $offer->name);
            $this->assertDatabaseHas('offers', ['name' => 'Test Offer']);
            $this->assertDatabaseHas('offer_validities', ['offer_id' => $offer->id]);
        } catch (ValidationException $e) {
            $this->fail('Validation failed: ' . json_encode($e->errors()));
        } catch (\Exception $e) {
            $this->fail('An error occurred: ' . $e->getMessage());
        }
    }

    public function test_store_offer_choice(): void
    {
        $offer = Offer::factory()->create();
        $offerChoiceData = [
            'offer_id' => $offer->id,
            'name' => 'Test Choice',
            'description' => 'Test Choice Description'
        ];

        $offerChoice = OfferService::storeOfferChoice($offer->id, $offerChoiceData);

        $this->assertInstanceOf(OfferChoice::class, $offerChoice);
        $this->assertEquals('Test Choice', $offerChoice->name);
        $this->assertDatabaseHas('offer_choices', ['name' => 'Test Choice']);
    }

    public function test_get_offer_by_id(): void
    {
        $offer = Offer::factory()->create();
        OfferValidity::factory()->create(['offer_id' => $offer->id]);
        OfferChoice::factory()->count(2)->create(['offer_id' => $offer->id]);

        $retrievedOffer = OfferService::getOfferById($offer->id);

        $this->assertIsArray($retrievedOffer);
        $this->assertEquals($offer->id, $retrievedOffer['id']);
        $this->assertArrayHasKey('validity', $retrievedOffer);
        $this->assertArrayHasKey('choices', $retrievedOffer);
        $this->assertCount(2, $retrievedOffer['choices']);
    }

    public function test_get_all_offers(): void
    {
        Offer::factory()->count(5)->create()->each(function ($offer) {
            OfferValidity::factory()->create(['offer_id' => $offer->id]);
            OfferChoice::factory()->create(['offer_id' => $offer->id]);
        });

        $offers = OfferService::getAllOffers();

        $this->assertIsArray($offers);
        $this->assertCount(5, $offers);
        $this->assertArrayHasKey('validity', $offers[0]);
        $this->assertArrayHasKey('choices', $offers[0]);
    }

    public function test_get_available_offers(): void
    {
        $customer = Customer::factory()->create();
        
        $validOffer = Offer::factory()->create([
            'offer_type_id' => OfferType::where('name', 'stalna')->first()->id
        ]);
        OfferValidity::factory()->create([
            'offer_id' => $validOffer->id,
            'valid_from' => now()->subDay(),
            'valid_until' => now()->addDay(),
        ]);

        $expiredOffer = Offer::factory()->create([
            'offer_type_id' => OfferType::where('name', 'jednokratna')->first()->id
        ]);
        OfferValidity::factory()->create([
            'offer_id' => $expiredOffer->id,
            'valid_from' => now()->subDays(2),
            'valid_until' => now()->subDay(),
        ]);

        $periodicalOffer = Offer::factory()->create([
            'offer_type_id' => OfferType::where('name', 'periodična')->first()->id
        ]);
        OfferValidity::factory()->create([
            'offer_id' => $periodicalOffer->id,
            'valid_from' => now()->subDay(),
            'valid_until' => now()->addDay(),
        ]);
        PeriodicalOfferDetail::factory()->create([
            'offer_id' => $periodicalOffer->id,
            'periodicity' => 'dnenva',
            'day_of_week' => now()->dayOfWeek,
            'time_of_day_start' => now()->subHour()->format('H:i:s'),
            'time_of_day_end' => now()->addHour()->format('H:i:s'),
        ]);

        $availableOffers = OfferService::getAvailableOffers($customer->id);

        $this->assertInstanceOf(LengthAwarePaginator::class, $availableOffers);
        $this->assertEquals(2, $availableOffers->count());
        $this->assertTrue($availableOffers->contains('id', $validOffer->id));
        $this->assertTrue($availableOffers->contains('id', $periodicalOffer->id));
    }

    public function test_has_used_offer(): void
    {
        $customer = Customer::factory()->create();
        $offer = Offer::factory()->create();
        $offerChoice = OfferChoice::factory()->create(['offer_id' => $offer->id]);

        $this->assertFalse(OfferService::hasUsedOffer($customer->id, $offer->id));

        // Simulate offer usage
        QrCode::factory()->create([
            'customer_id' => $customer->id,
            'offer_choice_id' => $offerChoice->id,
            'redeemed_at' => now(),
        ]);

        $this->assertTrue(OfferService::hasUsedOffer($customer->id, $offer->id));
    }

    public function test_can_use_periodical_offer(): void
    {
        $customer = Customer::factory()->create();
        $offer = Offer::factory()->create([
            'offer_type_id' => OfferType::where('name', 'periodična')->first()->id
        ]);
        $offerChoice = OfferChoice::factory()->create(['offer_id' => $offer->id]);
        
        PeriodicalOfferDetail::factory()->create([
            'offer_id' => $offer->id,
            'periodicity' => 'dnenva',
            'day_of_week' => now()->dayOfWeek,
            'time_of_day_start' => now()->subHour()->format('H:i:s'),
            'time_of_day_end' => now()->addHour()->format('H:i:s'),
        ]);

        $this->assertTrue(OfferService::canUsePerodicalOffer($customer->id, $offer));

        // Simulate offer usage from yesterday
        QrCode::factory()->create([
            'customer_id' => $customer->id,
            'offer_choice_id' => $offerChoice->id,
            'redeemed_at' => now()->subDay()->subHour(),
        ]);

        $this->assertTrue(OfferService::canUsePerodicalOffer($customer->id, $offer));

        // Simulate offer usage today
        QrCode::factory()->create([
            'customer_id' => $customer->id,
            'offer_choice_id' => $offerChoice->id,
            'redeemed_at' => now(),
        ]);

        $this->assertFalse(OfferService::canUsePerodicalOffer($customer->id, $offer));
    }

    public function test_periodical_offer_time_restrictions(): void
    {
        $customer = Customer::factory()->create();
        $offer = Offer::factory()->create([
            'offer_type_id' => OfferType::where('name', 'periodična')->first()->id
        ]);
        
        PeriodicalOfferDetail::factory()->create([
            'offer_id' => $offer->id,
            'periodicity' => 'dnenva',
            'day_of_week' => now()->dayOfWeek,
            'time_of_day_start' => '09:00:00',
            'time_of_day_end' => '17:00:00',
        ]);

        // Test offer availability during valid hours
        Carbon::setTestNow('10:00:00');
        $this->assertTrue(OfferService::canUsePerodicalOffer($customer->id, $offer));

        // Test offer availability outside valid hours
        Carbon::setTestNow('20:00:00');
        $this->assertFalse(OfferService::canUsePerodicalOffer($customer->id, $offer));

        // Test offer availability on wrong day
        Carbon::setTestNow(now()->addDay()->setTime(10, 0, 0));
        $this->assertFalse(OfferService::canUsePerodicalOffer($customer->id, $offer));
    }

    public function test_validates_offer_on_correct_day_of_week()
    {
        $detail = new PeriodicalOfferDetail(['day_of_week' => 1]); // Monday
        $this->assertTrue(OfferService::isCurrentlyValid($detail));

        $detail->day_of_week = 2; // Tuesday
        $this->assertFalse(OfferService::isCurrentlyValid($detail));
    }

    public function test_validates_offer_within_time_range()
    {
        $detail = new PeriodicalOfferDetail([
            'time_of_day_start' => '11:00:00',
            'time_of_day_end' => '13:00:00',
        ]);
        $this->assertTrue(OfferService::isCurrentlyValid($detail));

        $detail->time_of_day_start = '13:00:00';
        $detail->time_of_day_end = '14:00:00';
        $this->assertFalse(OfferService::isCurrentlyValid($detail));
    }

    public function test_handles_overnight_time_ranges()
    {
        $detail = new PeriodicalOfferDetail([
            'time_of_day_start' => '23:00:00',
            'time_of_day_end' => '01:00:00',
        ]);

        Carbon::setTestNow('2023-05-01 23:30:00'); // Monday at 11:30 PM
        $this->assertTrue(OfferService::isCurrentlyValid($detail));

        Carbon::setTestNow('2023-05-02 00:30:00'); // Tuesday at 12:30 AM
        $this->assertTrue(OfferService::isCurrentlyValid($detail));

        Carbon::setTestNow('2023-05-02 01:30:00'); // Tuesday at 1:30 AM
        $this->assertFalse(OfferService::isCurrentlyValid($detail));
    }

    public function test_validates_offer_with_no_restrictions()
    {
        $detail = new PeriodicalOfferDetail();
        $this->assertTrue(OfferService::isCurrentlyValid($detail));
    }

    public function test_validates_offer_with_both_day_and_time_restrictions()
    {
        $detail = new PeriodicalOfferDetail([
            'day_of_week' => 1,
            'time_of_day_start' => '11:00:00',
            'time_of_day_end' => '13:00:00',
        ]);
        $this->assertTrue(OfferService::isCurrentlyValid($detail));

        $detail->day_of_week = 2;
        $this->assertFalse(OfferService::isCurrentlyValid($detail));

        $detail->day_of_week = 1;
        $detail->time_of_day_start = '13:00:00';
        $detail->time_of_day_end = '14:00:00';
        $this->assertFalse(OfferService::isCurrentlyValid($detail));
    }

    public function test_get_all_used_orders(): void
    {
        $customer = Customer::factory()->create();
        
        $offer1 = Offer::factory()->create();
        $offerChoice1 = OfferChoice::factory()->create(['offer_id' => $offer1->id]);
        $offer2 = Offer::factory()->create();
        $offerChoice2 = OfferChoice::factory()->create(['offer_id' => $offer2->id]);

        QrCode::factory()->create([
            'customer_id' => $customer->id,
            'offer_choice_id' => $offerChoice1->id,
            'redeemed_at' => Carbon::now()->subDay(),
        ]);
        QrCode::factory()->create([
            'customer_id' => $customer->id,
            'offer_choice_id' => $offerChoice2->id,
            'redeemed_at' => Carbon::now()->subHours(2),
        ]);

        $usedOrders = OfferService::getAllUsedOrders($customer->id);

        $this->assertInstanceOf(LengthAwarePaginator::class, $usedOrders);
        $this->assertEquals(2, $usedOrders->count());
        
        foreach ($usedOrders as $order) {
            $this->assertArrayHasKey('redeemed_at', $order);
            $this->assertArrayHasKey('offer', $order);
            $this->assertNotNull($order['redeemed_at']);
        }

        $offerIds = $usedOrders->pluck('offer.id')->toArray();
        $this->assertContains($offer1->id, $offerIds);
        $this->assertContains($offer2->id, $offerIds);
    }
}