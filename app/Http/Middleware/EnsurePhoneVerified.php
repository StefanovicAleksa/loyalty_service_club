<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\CustomerService;
use Symfony\Component\HttpFoundation\Response;

class EnsurePhoneVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        if (!$user->customer || !CustomerService::checkIfVerified($user->customer->phone)) {
            session(['url.intended' => $request->url()]);
            
            return redirect()->route('verify.show')->with('warning', 'You must verify your phone number before accessing this page.');
        }

        return $next($request);
    }
}