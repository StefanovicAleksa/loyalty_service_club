<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\User;
use App\Validations\CustomerValidation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class CustomerService
{
    public static function storeCustomer(array $data): ?Customer
    {
        try {
            $validatedData = (new CustomerValidation())->validate($data);
            return DB::transaction(function () use ($validatedData) {
                $customer = Customer::create([
                    'first_name' => $validatedData['first_name'],
                    'last_name' => $validatedData['last_name'],
                    'phone' => $validatedData['phone'],
                ]);

                User::create([
                    'customer_id' => $customer->id,
                    'password' => Hash::make($validatedData['password']),
                ]);

                return $customer;
            });
        } catch (\Exception $e) {
            Log::error(__('customersdb.failed-store-customer') . ": " . $e->getMessage());
            return null;
        }
    }

    public static function validatePhone(string $phone): bool
    {
        try {
            $customer = Customer::where('phone', $phone)->firstOrFail();
            $customer->phone_verified_at = now();
            $customer->save();
            return true;
        } catch (\Exception $e) {
            Log::error(__('customersdb.failed-phone-validation') . ": " . $e->getMessage());
            return false;
        }
    }

    public static function customerExists(string $phone): bool
    {
        return Customer::where('phone', $phone)->exists();
    }

    public static function checkAuth(string $phone, string $password): bool
    {
        $customer = Customer::where('phone', $phone)->first();
        if (!$customer) {
            return false;
        }

        $user = $customer->user;
        return $user && Hash::check($password, $user->password);
    }

    public static function checkIfVerified(string $phone): bool
    {
        $customer = Customer::where('phone', $phone)->first();
        return $customer && $customer->phone_verified_at !== null;
    }

    public static function getCustomerByPhone(string $phone): ?Customer
    {
        return Customer::where('phone', $phone)->first();
    }

    public static function changePassword(User $user, string $newPassword): bool
    {
        try {
            $user->password = Hash::make($newPassword);
            $user->save();
            return true;
        } catch (\Exception $e) {
            Log::error(__('customersdb.failed-change-password') . ": " . $e->getMessage());
            return false;
        }
    }

    public static function changePasswordByPhone(string $phone, string $newPassword): bool
    {
        try {
            $customer = self::getCustomerByPhone($phone);
            if (!$customer) {
                return false;
            }

            $user = $customer->user;
            $user->password = Hash::make($newPassword);
            $user->save();
            return true;
        } catch (\Exception $e) {
            Log::error(__('customersdb.failed-change-password') . ": " . $e->getMessage());
            return false;
        }
    }
}