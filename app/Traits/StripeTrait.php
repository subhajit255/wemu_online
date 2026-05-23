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
}
