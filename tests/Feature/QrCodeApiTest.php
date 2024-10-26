<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\QrCode;
use App\Models\OfferChoice;
use App\Models\Offer;
use App\Models\OfferType;

class QrCodeApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test data
        $offerType = OfferType::factory()->create(['name' => 'Test Offer Type']);
        $offer = Offer::factory()->create([
            'name' => 'Test Offer',
            'offer_type_id' => $offerType->id
        ]);
        $offerChoice = OfferChoice::factory()->create([
            'offer_id' => $offer->id,
            'name' => 'Test Offer Choice',
            'description' => 'Test description',
            'picture' => 'test-picture.jpg'
        ]);
        $this->qrCode = QrCode::factory()->create([
            'offer_choice_id' => $offerChoice->id,
            'valid_until' => now()->addDay(),
        ]);
    }

    public function testRedeemQrCode()
    {
        $response = $this->postJson('/api/redeem-qr-code', [
            'qr_code_id' => $this->qrCode->id
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'QR code redeemed successfully.'
                 ]);

        // Test redeeming the same QR code again
        $response = $this->postJson('/api/redeem-qr-code', [
            'qr_code_id' => $this->qrCode->id
        ]);

        $response->assertStatus(400)
                 ->assertJson([
                     'success' => false,
                     'message' => 'QR code has already been redeemed.'
                 ]);
    }

    public function testGetRedeemedQrCodeOrderInformation()
    {
        $response = $this->getJson("/api/qr-code-order-info?qr_code_id={$this->qrCode->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'QR code information retrieved successfully.',
                     'data' => [
                         'offer_type' => 'Test Offer Type',
                         'offer_name' => 'Test Offer',
                         'offer_choice_name' => 'Test Offer Choice',
                         'offer_choice_picture' => 'test-picture.jpg',
                         'offer_choice_description' => 'Test description',
                     ]
                 ]);

        // Test with non-existent QR code
        $response = $this->getJson("/api/qr-code-order-info?qr_code_id=99999");

        $response->assertStatus(404)
                 ->assertJson([
                     'success' => false,
                     'message' => 'QR code not found.'
                 ]);
    }
}