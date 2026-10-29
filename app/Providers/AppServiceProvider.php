<?php

namespace App\Providers;

use App\Models\FinancialLedger;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Observers\AuditObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Product::observe(AuditObserver::class);
        User::observe(AuditObserver::class);
        FinancialLedger::observe(AuditObserver::class);
        Order::observe(AuditObserver::class);
    }
}
