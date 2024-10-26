<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Offer;
use App\Models\OfferChoice;
use App\Models\QrCode;
use App\Models\Customer;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ApiEndpointsTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    public function testGetAllMessages()
    {
        $response = $this->getJson('/api/message/all');
        $response->assertStatus(200)
                 ->assertJsonStructure(['data' => [['id', 'content', 'sent_at']]]);
    }

    public function testStoreMessage()
    {
        $data = ['content' => 'Test message'];
        $response = $this->postJson('/api/message/store', $data);
        $response->assertStatus(201)
                 ->assertJsonStructure(['message', 'data' => ['id', 'content', 'created_at']]);
    }

    public function testGetVerifiedPhoneNumbers()
    {
        $response = $this->getJson('/api/message/verified-phone-numbers');
        $response->assertStatus(200)
                 ->assertJsonStructure(['data']);
    }

    public function testRedeemQrCode()
    {
        $qrCode = QrCode::factory()->create();
        $response = $this->postJson('/api/qr-code/redeem', ['qr_code_id' => $qrCode->id]);
        $response->assertStatus(200)
                 ->assertJson(['success' => true]);
    }

    public function testGetRedeemedQrCodeOrderInformation()
    {
        $qrCode = QrCode::factory()->create(['redeemed_at' => now()]);
        $response = $this->getJson("/api/qr-code/order-info?qr_code_id={$qrCode->id}");
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'message',
                     'data' => ['offer_type', 'offer_name', 'offer_choice_name', 'offer_choice_picture', 'offer_choice_description', 'redeemed_at']
                 ]);
    }

    public function testGetOfferTypes()
    {
        $response = $this->getJson('/api/offers/types');
        $response->assertStatus(200)
                 ->assertJsonStructure(['success', 'data' => [['id', 'name']]]);
    }

    public function testGetAllOffers()
    {
        Offer::factory()->count(3)->create();
        $response = $this->getJson('/api/offers');
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => [['id', 'name', 'description', 'offer_type', 'validity', 'choices']]
                 ]);
    }

    public function testCreateOffer()
    {
        $data = [
            'name' => 'Test Offer',
            'description' => 'Test Description',
            'offer_type_id' => 1,
            'validity' => [
                'valid_from' => '2024-01-01',
                'valid_until' => '2024-12-31'
            ]
        ];
        $response = $this->postJson('/api/offers', $data);
        $response->assertStatus(201)
                 ->assertJsonStructure(['success', 'data' => ['id', 'name', 'description', 'offer_type_id', 'validity']]);
    }

    public function testGetSpecificOffer()
    {
        $offer = Offer::factory()->create();
        $response = $this->getJson("/api/offers/{$offer->id}");
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => ['id', 'name', 'description', 'offer_type', 'validity', 'choices']
                 ]);
    }

    public function testUpdateOffer()
    {
        $offer = Offer::factory()->create();
        $data = [
            'name' => 'Updated Offer Name',
            'description' => 'Updated Description'
        ];
        $response = $this->putJson("/api/offers/{$offer->id}", $data);
        $response->assertStatus(200)
                 ->assertJsonPath('data.name', 'Updated Offer Name')
                 ->assertJsonPath('data.description', 'Updated Description');
    }

    public function testDeleteOffer()
    {
        $offer = Offer::factory()->create();
        $response = $this->deleteJson("/api/offers/{$offer->id}");
        $response->assertStatus(200)
                 ->assertJson(['success' => true, 'message' => 'Offer deleted successfully']);
    }

    public function testListOfferChoices()
    {
        $offer = Offer::factory()->create();
        OfferChoice::factory()->count(2)->create(['offer_id' => $offer->id]);
        $response = $this->getJson("/api/offers/{$offer->id}/choices");
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => [['id', 'name', 'description', 'image_path']]
                 ]);
    }

    public function testCreateOfferChoice()
    {
        $offer = Offer::factory()->create();
        $file = UploadedFile::fake()->image('test.jpg');
        $data = [
            'name' => 'Test Choice',
            'description' => 'Test Choice Description',
            'picture' => $file
        ];
        $response = $this->postJson("/api/offers/{$offer->id}/choices", $data);
        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'success',
                     'data' => ['id', 'name', 'description', 'image_path', 'image_filename', 'image_size', 'image_uploaded_at']
                 ]);
    }

    public function testUpdateOfferChoice()
    {
        $offer = Offer::factory()->create();
        $choice = OfferChoice::factory()->create(['offer_id' => $offer->id]);
        $file = UploadedFile::fake()->image('updated.jpg');
        $data = [
            'name' => 'Updated Choice Name',
            'description' => 'Updated Choice Description',
            'picture' => $file
        ];
        $response = $this->postJson("/api/offers/{$offer->id}/choices/{$choice->id}", $data);
        $response->assertStatus(200)
                 ->assertJsonPath('data.name', 'Updated Choice Name')
                 ->assertJsonPath('data.description', 'Updated Choice Description');
    }

    public function testDeleteOfferChoice()
    {
        $offer = Offer::factory()->create();
        $choice = OfferChoice::factory()->create(['offer_id' => $offer->id]);
        $response = $this->deleteJson("/api/offers/{$offer->id}/choices/{$choice->id}");
        $response->assertStatus(200)
                 ->assertJson(['success' => true, 'message' => 'Offer choice deleted successfully']);
    }
}