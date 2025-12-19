<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;

class AppServiceProvider extends ServiceProvider
{

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    public function boot(): void
    {

        DB::statement("SET time_zone = '+04:00'");

        Gate::define("access-shipping-companies", function () {
            return Auth::id() === 1 || Auth::user()->hasPermission("صلاحية شركات الشحن");
        });

        Gate::define("access-products", function () {
            return Auth::id() === 1 || Auth::user()->hasPermission("صلاحية المنتوجات");
        });

        Gate::define("access-orders", function () {
            return Auth::id() === 1 || Auth::user()->hasPermission("صلاحية الطلبات");
        });

        Gate::define("access-ads", function () {
            return Auth::id() === 1 || Auth::user()->hasPermission("صلاحية اعلانات");
        });

        Gate::define("access-statistics", function () {
            return Auth::id() === 1 || Auth::user()->hasPermission("صلاحية الاحصائيات");
        });

        Gate::define('access-delete-any-thing', function () {
            return Auth::id() === 1;
        });

        Gate::define("access-websites", function () {
            return Auth::id() === 1;
        });
    }
}
