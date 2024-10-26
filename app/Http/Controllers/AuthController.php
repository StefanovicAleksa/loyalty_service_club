<?php

namespace App\Http\Controllers;

use App\Services\CustomerService;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request)
    {
        $phone = $request->validated('phone');

        if (!CustomerService::customerExists($phone)) {
            return back()->withErrors(['phone' => __('login.phone_not_exists')])->withInput();
        }

        if (!CustomerService::checkAuth($phone, $request->validated('password'))) {
            return back()->withErrors(['password' => __('login.invalid_password')])->withInput();
        }

        try {
            $customer = CustomerService::getCustomerByPhone($phone);
            Auth::loginUsingId($customer->user->id, $request->filled('remember'));
            $request->session()->regenerate();

            return redirect()->route('offers.index');
        } catch (\Exception $e) {
            return back()->withErrors(['login' => __('login.error_occurred')])->withInput();
        }
    }

    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(RegisterRequest $request)
    {
        $validatedData = $request->validated();
        $phone = $validatedData['phone'];

        if (CustomerService::customerExists($phone)) {
            return back()->withErrors(['phone' => __('register.phone_exists')])->withInput();
        }

        try {
            $customerData = [
                'first_name' => $validatedData['first_name'],
                'last_name' => $validatedData['last_name'],
                'phone' => $phone,
                'password' => $validatedData['password'],
            ];

            $customer = CustomerService::storeCustomer($customerData);

            if ($customer) {
                Auth::loginUsingId($customer->user->id);
                return redirect()->route('verify.show');
            } else {
                throw new \Exception(__('customersdb.failed-store-customer'));
            }
        } catch (\Exception $e) {
            return back()->withErrors(['phone' => $e->getMessage()])->withInput();
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    public function showChangePassword()
    {
        return view('auth.change-password');
    }
}