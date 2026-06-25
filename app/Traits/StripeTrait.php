<?php

namespace App\Traits;

/**
 * Trait StripeTrait
 * @package App\Traits
 */
trait StripeTrait
{
    /**
     * Create a Stripe customer.
     *
     * @param string $email
     * @param string|null $name
     * @param array $params Additional parameters to pass to Stripe
     * @return \Stripe\Customer
     */
    public function createCustomer(string $email, ?string $name = null, array $params = [])
    {
        // Ideally, you'd use config('services.stripe.secret') here instead of hardcoding
        $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));
        
        $payload = array_merge(array_filter([
            'email' => $email,
            'name' => $name,
        ]), $params);

        $customer = $stripe->customers->create($payload);

        return $customer;
    }

    /**
     * Create a recurring Stripe subscription.
     *
     * @param string $customerId The Stripe Customer ID (cus_...)
     * @param string $priceId The Stripe Price ID (price_...)
     * @param array $params Additional parameters to pass to Stripe
     * @return \Stripe\Subscription
     */
    public function createSubscription(string $customerId, string $priceId, array $params = [])
    {
        $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));
        
        $payload = array_merge([
            'customer' => $customerId,
            'items' => [
                ['price' => $priceId],
            ],
            // Expand the latest invoice and payment intent to handle initial 3DS auth if required
            'expand' => ['latest_invoice.payment_intent'],
        ], $params);

        $subscription = $stripe->subscriptions->create($payload);

        return $subscription;
    }

    /**
     * Create a Stripe Price and Product.
     *
     * @param string $name
     * @param float $amount
     * @param string $currency
     * @param string $interval
     * @param int $intervalCount
     * @return \Stripe\Price
     */
    public function createStripePrice(string $name, float $amount, string $currency, string $interval, int $intervalCount = 1, array $metadata = [])
    {
        $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));

        $price = $stripe->prices->create([
            'currency' => $currency,
            'unit_amount' => (int) ($amount * 100), // Stripe expects amounts in cents
            'recurring' => [
                'interval' => $interval,
                'interval_count' => $intervalCount,
            ],
            'product_data' => [
                'name' => $name,
                'metadata' => $metadata,
            ],
            'metadata' => $metadata,
        ]);

        return $price;
    }
    public function attachPaymentMethodToCustomer(string $customerId, string $paymentMethodId)
    {
        $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));

        // Attach Payment Method
        $paymentMethod = $stripe->paymentMethods->attach(
            $paymentMethodId,
            ['customer' => $customerId]
        );

        // Set Default Payment Method
        $stripe->customers->update(
            $customerId,
            ['invoice_settings' => ['default_payment_method' => $paymentMethod->id]]
        );
        
        return $paymentMethod;
    }

    /**
     * Create a Stripe Checkout Session for subscription
     *
     * @param string $customerId The Stripe Customer ID
     * @param string $priceId The Stripe Price ID
     * @param string $successUrl URL to redirect upon success
     * @param string $cancelUrl URL to redirect upon cancellation
     * @param array $metadata Additional metadata
     * @param array $subscriptionData Additional subscription data (like trial_end)
     * @return \Stripe\Checkout\Session
     */
    public function createCheckoutSession(string $customerId, string $priceId, string $successUrl, string $cancelUrl, array $metadata = [], array $subscriptionData = [])
    {
        $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));

        $sessionPayload = [
            'customer' => $customerId,
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price' => $priceId,
                'quantity' => 1,
            ]],
            'mode' => 'subscription',
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
            'metadata' => $metadata,
            'subscription_data' => array_merge([
                'metadata' => $metadata,
            ], $subscriptionData),
        ];

        $session = $stripe->checkout->sessions->create($sessionPayload);

        return $session;
    }
}
