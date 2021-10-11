<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Stripe\StripeClient;
use Symfony\Component\Mercure\Hub;
use Symfony\Component\Mercure\Jwt\StaticTokenProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(Hub::class, function($app) {
            $url = 'http://mercure/.well-known/mercure';
            $token = env('MERCURE_JWT_TOKEN');
            $jwtProvider = new StaticTokenProvider($token);
            return new Hub($url, $jwtProvider);
        });

        $this->app->bind(StripeClient::class, function($app) {
            return new StripeClient(env('STRIPE_API_SECRET'));
        });
    }
}
