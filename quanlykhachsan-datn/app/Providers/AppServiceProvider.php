<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
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
    // Định nghĩa Gate để kiểm tra quyền truy cập admin
    public function boot(): void
    {
        Gate::define('access-admin', function ($user) {
            return in_array(strtoupper(trim($user->role)), ['ADMIN', 'NHANVIEN']);
        });
    }
}
