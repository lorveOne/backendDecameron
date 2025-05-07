<?php

namespace App\Providers;


use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
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
        $this->configureRateLimiting();

        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->namespace ?? null)
            ->group(base_path('routes/HotelRouter.php'));

        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->namespace ?? null)
            ->group(base_path('routes/HabitaInfoRouter.php'));
    }

      /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(120)->by(optional($request->user())->id ?: $request->ip());
        });
    }
}
