<?php

/** @var \Laravel\Lumen\Routing\Router $router */


$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->get('/debug', function () {
    return [
        'env' => env('APP_ENV'),
        'auth' => config('auth')
    ];
});

$router->post('/debug_mercure', function(\Symfony\Component\Mercure\Hub $hub) {
    $msg = 'my message from controller';
    $topics = [
        'public-topic-1',
        'public-topic-2',
    ];
    $hub->publish(new \Symfony\Component\Mercure\Update($topics, $msg));

    return response()->json(['message' => 'published']);
});

$router->post('/stripe-hooks', 'StripeHooksController@handle');

$router->group(['prefix' => 'api'], function () use ($router) {

    $router->group(['prefix' => 'auth'], function() use ($router) {
        $router->post('login', 'AuthController@login');
    });

    $router->group(['prefix' => 'users'], function() use ($router) {
        $router->post('', 'UserController@register');
        $router->get('me', 'UserController@me');
        $router->patch('{userId}/create-stripe-customer', 'UserController@createStripeCustomer');
        $router->post('{userId}/pictures', 'UserController@uploadProfilePicture');
    });

    $router->group(['prefix' => 'shop'], function() use ($router) {
        $router->get('config', 'ShopController@config');

        $router->post('subscriptions', 'ShopController@subscriptions');
    });
});

