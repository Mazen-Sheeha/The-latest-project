<?php

use App\Http\Controllers\AdsetController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\BlockedNumberController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\DomainController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PixelController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ShippingCompanyController;
use App\Http\Controllers\StatisticController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WebsiteController;
use App\Models\Permission; // For Migration To Add Permissions
use App\Models\User; // For Migrations To Add First Admin
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::fallback(function () {
    if (Auth::check())
        return to_route("home");
    return to_route("login");
});

Route::controller(AuthController::class)->group(function () {
    Route::middleware("RedirectIfAuthenticated")->group(function () {
        Route::get("/login", 'showLogin')->name('showLogin');
        Route::post('/login', 'login')->name('login');
    });
    Route::post('/logout', 'logout')->name('logout');
});

Route::middleware("auth")->group(function () {
    Route::get("/home", [HomeController::class, 'home'])->name('home');
    Route::get('/statistics', [StatisticController::class, 'statistics'])->name('statistics')->middleware("RedirectIfCannot:access-statistics");
    Route::resource('/admins', UserController::class)
        ->only(['index', 'show', 'create', 'store', 'edit', 'update', 'destroy']);
    Route::resource('/shipping_companies', ShippingCompanyController::class)
        ->only(['index', 'show', 'create', 'store', 'edit', 'update', 'destroy'])
        ->middleware("RedirectIfCannot:access-shipping-companies");
    Route::middleware("RedirectIfCannot:access-products")->group(function () {
        Route::resource("/products", ProductController::class)
            ->only(['index', 'show', 'create', 'store', 'edit', 'update', 'destroy']);
        Route::post('{product}/increase_stock', [ProductController::class, 'increaseStock'])
            ->name('products.increase_stock');
        Route::post('/products/{product}/changeStatus', [ProductController::class, 'changeStatus'])->name('products.changeStatus');
    });
    Route::middleware("RedirectIfCannot:access-orders")->group(function () {
        Route::resource("/orders", OrderController::class)
            ->only(['index', 'show', 'edit', 'update', 'create', 'store', 'destroy']);
        Route::get('/notifications/fetch', [NotificationController::class, 'fetch'])->name('notifications.fetch');
        Route::controller(OrderController::class)->group(function () {
            Route::get('/orders/export/excel', 'export')->name('orders.export');
            Route::post('/orders/{order}/order_status', 'changeOrderStatus')->name('orders.changeOrderStatus');
            Route::post('/orders/{order}/payment_status', 'changePaymentStatus')->name('orders.changePaymentStatus');
            Route::post('/orders/{order}/tracking_number', 'changeTrackingNumber')->name('orders.changeTrackingNumber');
        });
        Route::controller(BlockedNumberController::class)->group(function () {
            Route::delete('/blocked_numbers', 'destroy')->name('blocked_numbers.destroy');
            Route::post('/blocked_numbers', 'store')->name('blocked_numbers.store');
        });
    });
    Route::middleware("RedirectIfCannot:access-ads")->group(function () {
        Route::resource('/adsets', AdsetController::class)
            ->only(['index', 'store', 'edit', 'update', 'destroy']);
        Route::post('/adsets/{adset}/active', [AdsetController::class, 'changeActive'])->name('adsets.changeActive');
        Route::get("/adset_statistics", [AdsetController::class, 'statisticsIndex'])->name('adsets.statistics');

        Route::resource("/campaigns", CampaignController::class)
            ->only(['index', 'store', 'edit', 'update', 'destroy']);
        Route::post('/campaigns/{campaign}/active', [CampaignController::class, 'changeActive'])->name('campaigns.changeActive');
        Route::resource('/budgets', BudgetController::class)
            ->only(['index', 'store', 'edit', 'update', 'destroy']);
        Route::get("/campaign_statistics", [CampaignController::class, 'statisticsIndex'])->name('campaigns.statistics');
    });
    Route::middleware("RedirectIfCannot:access-websites")->group(function () {
        Route::resource('/websites', WebsiteController::class)
            ->only(['index', 'store', 'edit', 'update', 'destroy']);
    });

    Route::middleware("RedirectIfCannot:access-domains")->group(function () {
        Route::resource('/domains', DomainController::class);
    });

    Route::middleware("RedirectIfCannot:access-pages")->group(function () {
        Route::patch('/pages/{page}/toggle-active', [PageController::class, 'toggleActive'])->name('pages.toggleActive');
        Route::resource('/pages', PageController::class);
        Route::delete('/pages/{page}/image', [PageController::class, 'deleteImage'])->name('pages.image.delete');
    });

    // Pixels Routes
    Route::middleware("RedirectIfCannot:access-pages")->group(function () {
        Route::resource('/pixels', PixelController::class);
    });

});

Route::get('/test-domain', function () {
    return request()->getHost();
});

// Route::middleware('resolveDomain')->group(function () {
Route::get('buy/{page:slug}', [PageController::class, 'showBuyPage'])->name('pages.buy');
Route::get('buy/upsell/{slug}/{orderId?}', [PageController::class, 'showUpsellPage'])->name('pages.showUpsellPage');
Route::post('buy/{page:slug}', [PageController::class, 'submitOrder'])->name('pages.submitOrder');
Route::post('buy/upsell/submit', [PageController::class, 'submitOrderFromUpsellPage'])->name('pages.submitOrderFromUpsellPage');
// });

// Necessary Data To Migrate
// User::create(['id' => 1, 'name' => "admin", 'email' => "admin@admin.com", 'password' => bcrypt("123456")]);
// Permission::create(['name' => 'صلاحية الطلبات']);
// Permission::create(['name' => 'صلاحية اعلانات']);
// Permission::create(['name' => 'صلاحية المنتوجات']);
// Permission::create(['name' => 'صلاحية ادارة المدراء']);
// Permission::create(['name' => "صلاحية شركات الشحن"]);
// Permission::create(['name' => "صلاحية الاحصائيات"]);
// Permission::create(['name' => "صلاحية الصفحات"]);
// Permission::create(['name' => "صلاحية دومينات الصفحات"]);
