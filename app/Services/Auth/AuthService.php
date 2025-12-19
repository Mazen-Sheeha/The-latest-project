<?php

namespace App\Services\Auth;

use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class AuthService
{

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->only(['email', 'password']);
        if (Auth::attempt($credentials)) {
            if (Gate::allows('access-statistics')) return to_route('home')->with(['success' => "تم تسجيل الدخول بنجاح"]);
            if (Gate::allows('access-products')) return to_route('products.index')->with(['success' => "تم تسجيل الدخول بنجاح"]);
            if (Gate::allows('access-orders')) return to_route('orders.index')->with(['success' => "تم تسجيل الدخول بنجاح"]);
            if (Gate::allows('access-shipping-companies')) return to_route('shipping_companies.index')->with(['success' => "تم تسجيل الدخول بنجاح"]);
            if (Gate::allows('access-ads')) return to_route('adsets.index')->with(['success' => "تم تسجيل الدخول بنجاح"]);
            if (Gate::allows('access-admins')) return to_route('adsets.index')->with(['success' => "تم تسجيل الدخول بنجاح"]);
            if (Gate::allows('viewAny', User::class)) return to_route('adsets.index')->with(['success' => "تم تسجيل الدخول بنجاح"]);
            if (Gate::allows('access-websites')) return to_route('websites.index')->with(['success' => "تم تسجيل الدخول بنجاح"]);
        }
        return back()->withErrors(["message" => "البيانات غير صالحة"]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->regenerateToken();
        $request->session()->invalidate();
        return to_route('showLogin')->with(['success' => 'تم تسجيل الخروج بنجاح']);
    }
}
