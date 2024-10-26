<?php

namespace Tests\Unit;

use App\Models\Customer;
use App\Models\Offer;
use App\Models\OfferChoice;
use App\Models\QrCode;
use App\Services\QrCodeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QrCodeServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_qr_code(): void
    {
        $customer = Customer::factory()->create();
        $offer = Offer::factory()->create();
        $offerChoice = OfferChoice::factory()->create(['offer_id' => $offer->id]);

        $qrCodeData = [
            'customer_id' => $customer->id,
            'offer_choice_id' => $offerChoice->id
        ];

        $qrCode = QrCodeService::createQrCode($qrCodeData);

        $this->assertInstanceOf(QrCode::class, $qrCode);
        $this->assertEquals($customer->id, $qrCode->customer_id);
        $this->assertEquals($offerChoice->id, $qrCode->offer_choice_id);
    }

    public function test_check_qr_code_validity(): void
    {
        $expiredQrCode = QrCode::factory()->create([
            'valid_until' => now()->subHour(),
            'redeemed_at' => null,
        ]);

        $this->assertFalse(QrCodeService::checkQrCodeValidity($expiredQrCode->id));

        $validQrCode = QrCode::factory()->create([
            'valid_until' => now()->addHour(),
            'redeemed_at' => null,
        ]);

        $this->assertTrue(QrCodeService::checkQrCodeValidity($validQrCode->id));
    }

    public function test_get_customer_qr_code(): void
    {
        $customer = Customer::factory()->create();
        
        $validQrCode = QrCode::factory()->create([
            'customer_id' => $customer->id,
            'valid_until' => now()->addHour(),
            'redeemed_at' => null,
        ]);

        $retrievedQrCode = QrCodeService::getCustomerQrCode($customer->id);

        $this->assertInstanceOf(QrCode::class, $retrievedQrCode);
        $this->assertEquals($validQrCode->id, $retrievedQrCode->id);
    }

    public function test_redeem_qr_code(): void
    {
        $qrCode = QrCode::factory()->create([
            'valid_until' => now()->addHour(),
            'redeemed_at' => null,
        ]);

        $result = QrCodeService::redeemQrCode($qrCode->id);

        $this->assertTrue($result);
        $this->assertNotNull($qrCode->fresh()->redeemed_at);
    }
}