<?php

namespace Tests\Unit;

use App\Models\Customer;
use App\Models\User;
use App\Services\CustomerService;
use App\Validations\CustomerValidation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;

class CustomerServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_customer(): void
    {
        $customerData = [
            'name' => 'John Doe',
            'phone' => '+1234567890',
            'password' => 'password123'
        ];

        $customer = CustomerService::storeCustomer($customerData);

        $this->assertInstanceOf(Customer::class, $customer);
        $this->assertDatabaseHas('customers', ['name' => 'John Doe', 'phone' => '+1234567890']);
        $this->assertDatabaseHas('users', ['customer_id' => $customer->id]);
    }

    public function test_validate_phone(): void
    {
        $customer = Customer::factory()->create(['phone_verified_at' => null]);

        $result = CustomerService::validatePhone($customer->phone);

        $this->assertTrue($result);
        $this->assertNotNull($customer->fresh()->phone_verified_at);
    }

    public function test_customer_exists(): void
    {
        $customer = Customer::factory()->create();

        $exists = CustomerService::customerExists($customer->phone);
        $this->assertTrue($exists);

        $notExists = CustomerService::customerExists('+9999999999');
        $this->assertFalse($notExists);
    }

    public function test_check_auth(): void
    {
        $customer = Customer::factory()->create();
        $user = User::factory()->create(['customer_id' => $customer->id, 'password' => Hash::make('password')]);

        $validAuth = CustomerService::checkAuth($customer->phone, 'password');
        $this->assertTrue($validAuth);

        $invalidAuth = CustomerService::checkAuth($customer->phone, 'wrong_password');
        $this->assertFalse($invalidAuth);
    }

    public function test_check_if_verified(): void
    {
        $verifiedCustomer = Customer::factory()->create(['phone_verified_at' => now()]);
        $unverifiedCustomer = Customer::factory()->create(['phone_verified_at' => null]);

        $isVerified = CustomerService::checkIfVerified($verifiedCustomer->phone);
        $this->assertTrue($isVerified);

        $isNotVerified = CustomerService::checkIfVerified($unverifiedCustomer->phone);
        $this->assertFalse($isNotVerified);
    }
}