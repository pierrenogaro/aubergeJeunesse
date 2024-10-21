<?php

namespace App\Service;

use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;
use Stripe\Stripe;

class StripeService
{
    public function __construct(string $stripeSecretKey)
    {
        Stripe::setApiKey($stripeSecretKey);
    }

    /**
     * @throws ApiErrorException
     */
    public function createCheckoutSession(array $lineItems): Session
    {
        return Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [$lineItems],
            'mode' => 'payment',
            'success_url' => 'http://localhost:8000/payment/success',
            'cancel_url' => 'http://localhost:8000/payment/cancel'
        ]);
    }
}
