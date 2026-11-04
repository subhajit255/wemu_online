<?php

namespace App\Traits;

/**
 * Trait StripeTrait
 * @package App\Traits
 */
trait StripeTrait
{
    /**
     * Ensure the user has a valid Stripe customer ID, creating and saving one if missing or invalid.
     *
     * @param \App\Models\User $user
     * @return string Stripe customer ID
     */
    public function ensureStripeCustomer(\App\Models\User $user): string
    {
        $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));

        // If stripe_id exists, verify it still exists in the active Stripe account (e.g. test vs live mode)
        if (!empty($user->stripe_id)) {
            try {
                $customer = $stripe->customers->retrieve($user->stripe_id);
                if (empty($customer->deleted)) {
                    return $customer->id;
                }
            } catch (\Stripe\Exception\InvalidRequestException $e) {
                // Customer not found in current Stripe account (e.g. switched from test to live mode)
                logger()->info("Stripe customer {$user->stripe_id} not found in current Stripe account, recreating...");
            } catch (\Exception $e) {
                logger()->warning("Could not verify Stripe customer {$user->stripe_id}: " . $e->getMessage());
            }
        }

        // Create new customer
        $customer = $stripe->customers->create(array_filter([
            'email' => $user->email,
            'name' => $user->name,
            'metadata' => [
                'user_id' => (string) $user->id,
            ],
        ]));

        // Immediately persist to users table
        $user->stripe_id = $customer->id;
        $user->save();

        return $customer->id;
    }

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
            'payment_behavior' => 'default_incomplete',
            'payment_settings' => ['save_default_payment_method' => 'on_subscription'],
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
            'currency' => strtolower($currency),
            'unit_amount' => (int) round($amount * 100), // Stripe expects amounts in cents
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
    public function cancelSubscription(string $subscriptionId)
    {
        $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));
        return $stripe->subscriptions->cancel($subscriptionId);
    }
}
