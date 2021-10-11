<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Stripe\StripeClient;


class ShopController extends Controller
{
    private $stripeClient;

    public function __construct(StripeClient $stripeClient)
    {
        $this->stripeClient = $stripeClient;

        $this->middleware('auth', ['except' => ['config']]);
    }

    public function config()
    {
        $prices = $this->stripeClient->prices->all();

        return response()->json([
            'prices' => $prices,
            'publishableKey' => env('STRIPE_API_KEY')
        ]);
    }

    public function subscriptions(Request $request)
    {
        $this->validate($request, [
            'price_id' => 'required'
        ]);

        $priceId = $request->get('price_id');
        $user = Auth::user();

        if ($user->stripe_id === null) {
            $customer = $this->stripeClient->customers->create([
                'email' => $user->email
            ]);
            $user->stripe_id = $customer->id;
            $user->save();
        } else {
            $customer = $this->stripeClient->customers->retrieve($user->stripe_id);
        }

        $price = $this->stripeClient->prices->retrieve($priceId);

        $subscription = $this->stripeClient->subscriptions->create([
            'customer' => $customer->id,
            'items' => [[
                'price' => $priceId
            ]],
            'payment_behavior' => 'default_incomplete'
        ]);

        return response()->json([
            'subscription_id' => $subscription->id
        ]);
    }
}
