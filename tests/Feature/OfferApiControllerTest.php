<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Offer;
use App\Models\OfferChoice;
use App\Models\OfferType;
use Illuminate\Http\UploadedFile;

class OfferApiControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testGetOfferTypes()
    {
        $response = $this->getJson('/api/offers/types');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => ['id', 'name']
                ]
            ]);
    }

    public function testIndex()
    {
        Offer::factory()->count(3)->create();

        $response = $this->getJson('/api/offers');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => ['id', 'name', 'description', 'offer_type_id']
                ]
            ]);
    }

    public function testStore()
    {
        $offerType = OfferType::first();

        $offerData = [
            'name' => $this->faker->word,
            'description' => $this->faker->sentence,
            'offer_type_id' => $offerType->id,
            'validity' => [
                'valid_from' => now()->toDateString(),
                'valid_until' => now()->addMonth()->toDateString(),
            ],
        ];

        $response = $this->postJson('/api/offers', $offerData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'data' => ['id', 'name', 'description', 'offer_type_id', 'validity']
            ]);
    }

    public function testShow()
    {
        $offer = Offer::factory()->create();

        $response = $this->getJson("/api/offers/{$offer->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => ['id', 'name', 'description', 'offer_type_id', 'validity']
            ]);
    }

    public function testUpdate()
    {
        $offer = Offer::factory()->create();
        $newData = [
            'name' => 'Updated Offer Name',
            'description' => 'Updated description',
            'offer_type_id' => $offer->offer_type_id,
            'validity' => [
                'valid_from' => now()->toDateString(),
                'valid_until' => now()->addMonth()->toDateString(),
            ],
        ];

        $response = $this->putJson("/api/offers/{$offer->id}", $newData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => ['id', 'name', 'description', 'offer_type_id', 'validity']
            ])
            ->assertJsonPath('data.name', 'Updated Offer Name');
    }

    public function testDestroy()
    {
        $offer = Offer::factory()->create();

        $response = $this->deleteJson("/api/offers/{$offer->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Offer deleted successfully'
            ]);

        $this->assertDatabaseMissing('offers', ['id' => $offer->id]);
    }

    public function testListOfferChoices()
    {
        $offer = Offer::factory()->create();
        OfferChoice::factory()->count(3)->create(['offer_id' => $offer->id]);

        $response = $this->getJson("/api/offers/{$offer->id}/choices");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    '*' => ['id', 'offer_id', 'name', 'description']
                ]
            ]);
    }

    public function testStoreOfferChoice()
    {
        $offer = Offer::factory()->create();
        $choiceData = [
            'name' => $this->faker->word,
            'description' => $this->faker->sentence,
            'picture' => UploadedFile::fake()->image('choice.jpg'),
        ];

        $response = $this->postJson("/api/offers/{$offer->id}/choices", $choiceData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'data' => ['id', 'offer_id', 'name', 'description', 'picture']
            ]);
    }

    public function testUpdateOfferChoice()
    {
        $offer = Offer::factory()->create();
        $choice = OfferChoice::factory()->create(['offer_id' => $offer->id]);
        $newData = [
            'name' => 'Updated Choice Name',
            'description' => 'Updated choice description',
        ];

        $response = $this->putJson("/api/offers/{$offer->id}/choices/{$choice->id}", $newData);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => ['id', 'offer_id', 'name', 'description']
            ])
            ->assertJsonPath('data.name', 'Updated Choice Name');
    }

    public function testDestroyOfferChoice()
    {
        $offer = Offer::factory()->create();
        $choice = OfferChoice::factory()->create(['offer_id' => $offer->id]);

        $response = $this->deleteJson("/api/offers/{$offer->id}/choices/{$choice->id}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Offer choice deleted successfully'
            ]);

        $this->assertDatabaseMissing('offer_choices', ['id' => $choice->id]);
    }
}