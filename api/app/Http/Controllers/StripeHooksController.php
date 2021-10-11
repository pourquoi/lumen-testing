<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class StripeHooksController extends Controller
{
    public function handle(Request $request)
    {
        $sig_header = $request->header('stripe-signature');

        $event = null;

        try {
            $event = \Stripe\Webhook::constructEvent(
                $request->getContent(), $sig_header, env('STRIPE_ENDPOINT_SECRET')
            );
        } catch(\UnexpectedValueException $e) {
            abort(400);
        }

        Log::info(json_encode($event));

        switch ($event->type) {
            case 'payment_intent.succeeded':
                $paymentIntent = $event->data->object;
                break;
            case 'payment_method.attached':
                $paymentMethod = $event->data->object;
                break;
            default:
                echo 'Received unknown event type ' . $event->type;
        }

        return response('', 200);
    }
}
