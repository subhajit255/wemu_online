<?php

namespace Tests\Feature;

use App\Models\Subscription;
use App\Models\User;
use App\Models\UserSubscription;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Laravel\Passport\Passport;
use Tests\TestCase;

class MyCurrentSubscriptionTest extends TestCase
{
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        config(['database.default' => 'sqlite']);
        config(['database.connections.sqlite.database' => ':memory:']);

        // Set up in-memory tables
        Schema::dropIfExists('user_subscriptions');
        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('users');

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->nullable();
            $table->string('name')->nullable();
            $table->string('email')->unique();
            $table->string('password')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->nullable();
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->text('features')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->string('interval')->default('month');
            $table->integer('max_song_skips')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->tinyInteger('available_for')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('user_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('subscription_id')->nullable();
            $table->string('stripe_id')->nullable();
            $table->string('stripe_status')->nullable();
            $table->timestamp('current_period_start')->nullable();
            $table->timestamp('current_period_end')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->date('started_on')->nullable();
            $table->date('ended_at')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });

        $this->user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);
    }

    /**
     * Test 1: Active subscription with started_on <= today and ended_at >= today returns data.
     */
    public function test_active_subscription_within_dates_returns_subscription(): void
    {
        $plan = Subscription::create([
            'name' => 'Premium Individual',
            'price' => 9.99,
            'interval' => 'month',
        ]);

        $sub = UserSubscription::create([
            'user_id' => $this->user->id,
            'subscription_id' => $plan->id,
            'status' => 1,
            'started_on' => Carbon::today()->subDays(5)->toDateString(),
            'ended_at' => Carbon::today()->addDays(25)->toDateString(),
        ]);

        Passport::actingAs($this->user);
        $response = $this->getJson('/api/my-current-subscription');

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'response_code' => 200,
                'message' => 'Subscription fetched successfully',
            ]);

        $data = $response->json('data');
        $this->assertNotNull($data);
        $this->assertEquals($sub->id, $data['id']);
        $this->assertEquals('Premium Individual', $data['name']);
        $this->assertEquals(9.99, $data['price']);
    }

    /**
     * Test 2: Expired subscription (ended_at in the past) returns null even if status is 1.
     */
    public function test_expired_subscription_returns_null(): void
    {
        $plan = Subscription::create([
            'name' => 'Old Expired Plan',
            'price' => 9.99,
        ]);

        UserSubscription::create([
            'user_id' => $this->user->id,
            'subscription_id' => $plan->id,
            'status' => 1,
            'started_on' => Carbon::today()->subDays(40)->toDateString(),
            'ended_at' => Carbon::today()->subDays(10)->toDateString(), // Expired 10 days ago
        ]);

        Passport::actingAs($this->user);
        $response = $this->getJson('/api/my-current-subscription');

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'response_code' => 200,
                'data' => null,
            ]);
    }

    /**
     * Test 3: Future subscription (started_on in the future) returns null.
     */
    public function test_future_subscription_returns_null(): void
    {
        $plan = Subscription::create([
            'name' => 'Future Plan',
            'price' => 14.99,
        ]);

        UserSubscription::create([
            'user_id' => $this->user->id,
            'subscription_id' => $plan->id,
            'status' => 1,
            'started_on' => Carbon::today()->addDays(5)->toDateString(), // Starts in 5 days
            'ended_at' => Carbon::today()->addDays(35)->toDateString(),
        ]);

        Passport::actingAs($this->user);
        $response = $this->getJson('/api/my-current-subscription');

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'response_code' => 200,
                'data' => null,
            ]);
    }

    /**
     * Test 4: Subscription with status = 0 (inactive/cancelled) returns null.
     */
    public function test_inactive_status_subscription_returns_null(): void
    {
        $plan = Subscription::create([
            'name' => 'Cancelled Plan',
            'price' => 9.99,
        ]);

        UserSubscription::create([
            'user_id' => $this->user->id,
            'subscription_id' => $plan->id,
            'status' => 0, // Inactive
            'started_on' => Carbon::today()->subDays(5)->toDateString(),
            'ended_at' => Carbon::today()->addDays(25)->toDateString(),
        ]);

        Passport::actingAs($this->user);
        $response = $this->getJson('/api/my-current-subscription');

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'response_code' => 200,
                'data' => null,
            ]);
    }

    /**
     * Test 5: Fallback when ended_at is null but current_period_end is in the future.
     */
    public function test_fallback_when_ended_at_is_null_but_current_period_end_is_future(): void
    {
        $plan = Subscription::create([
            'name' => 'Webhook Plan',
            'price' => 19.99,
        ]);

        $sub = UserSubscription::create([
            'user_id' => $this->user->id,
            'subscription_id' => $plan->id,
            'status' => 1,
            'started_on' => Carbon::today()->toDateString(),
            'ended_at' => null,
            'current_period_end' => Carbon::today()->addDays(30),
        ]);

        Passport::actingAs($this->user);
        $response = $this->getJson('/api/my-current-subscription');

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertNotNull($data);
        $this->assertEquals($sub->id, $data['id']);
    }

    /**
     * Test 6: Fallback when ended_at is null but current_period_end is expired.
     */
    public function test_fallback_when_ended_at_is_null_and_current_period_end_is_past(): void
    {
        $plan = Subscription::create([
            'name' => 'Expired Webhook Plan',
            'price' => 19.99,
        ]);

        UserSubscription::create([
            'user_id' => $this->user->id,
            'subscription_id' => $plan->id,
            'status' => 1,
            'started_on' => Carbon::today()->subDays(60)->toDateString(),
            'ended_at' => null,
            'current_period_end' => Carbon::today()->subDays(30),
            'ends_at' => Carbon::today()->subDays(30),
        ]);

        Passport::actingAs($this->user);
        $response = $this->getJson('/api/my-current-subscription');

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'response_code' => 200,
                'data' => null,
            ]);
    }

    /**
     * Test 7: Subscription that starts and ends today is valid today.
     */
    public function test_subscription_valid_on_boundary_dates(): void
    {
        $plan = Subscription::create([
            'name' => 'Daily Pass',
            'price' => 1.99,
        ]);

        $sub = UserSubscription::create([
            'user_id' => $this->user->id,
            'subscription_id' => $plan->id,
            'status' => 1,
            'started_on' => Carbon::today()->toDateString(),
            'ended_at' => Carbon::today()->toDateString(),
        ]);

        Passport::actingAs($this->user);
        $response = $this->getJson('/api/my-current-subscription');

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertNotNull($data);
        $this->assertEquals($sub->id, $data['id']);
    }

    /**
     * Test 8: Multiple subscriptions where old one expired but new one is active returns the active one.
     */
    public function test_returns_latest_active_when_previous_is_expired(): void
    {
        $planOld = Subscription::create(['name' => 'Old Plan', 'price' => 5]);
        $planNew = Subscription::create(['name' => 'New Plan', 'price' => 10]);

        // Old expired subscription
        UserSubscription::create([
            'user_id' => $this->user->id,
            'subscription_id' => $planOld->id,
            'status' => 0,
            'started_on' => Carbon::today()->subMonths(2)->toDateString(),
            'ended_at' => Carbon::today()->subMonth()->toDateString(),
        ]);

        // New active subscription
        $newSub = UserSubscription::create([
            'user_id' => $this->user->id,
            'subscription_id' => $planNew->id,
            'status' => 1,
            'started_on' => Carbon::today()->subDay()->toDateString(),
            'ended_at' => Carbon::today()->addMonth()->toDateString(),
        ]);

        Passport::actingAs($this->user);
        $response = $this->getJson('/api/my-current-subscription');

        $response->assertStatus(200);
        $data = $response->json('data');
        $this->assertNotNull($data);
        $this->assertEquals($newSub->id, $data['id']);
        $this->assertEquals('New Plan', $data['name']);
    }

    /**
     * Test 9: Does not leak another user's active subscription.
     */
    public function test_does_not_return_another_users_active_subscription(): void
    {
        $otherUser = User::create([
            'name' => 'Other User',
            'email' => 'other@example.com',
            'password' => bcrypt('password'),
        ]);

        $plan = Subscription::create(['name' => 'Other User Plan', 'price' => 15]);

        UserSubscription::create([
            'user_id' => $otherUser->id,
            'subscription_id' => $plan->id,
            'status' => 1,
            'started_on' => Carbon::today()->subDays(5)->toDateString(),
            'ended_at' => Carbon::today()->addDays(25)->toDateString(),
        ]);

        Passport::actingAs($this->user);
        $response = $this->getJson('/api/my-current-subscription');

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'response_code' => 200,
                'data' => null,
            ]);
    }

    /**
     * Test 10: Subscription expired yesterday (ended_at = yesterday) returns null.
     */
    public function test_subscription_expired_yesterday_returns_null(): void
    {
        $plan = Subscription::create(['name' => 'Yesterday Plan', 'price' => 9.99]);

        UserSubscription::create([
            'user_id' => $this->user->id,
            'subscription_id' => $plan->id,
            'status' => 1,
            'started_on' => Carbon::today()->subMonth()->toDateString(),
            'ended_at' => Carbon::yesterday()->toDateString(),
        ]);

        Passport::actingAs($this->user);
        $response = $this->getJson('/api/my-current-subscription');

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'response_code' => 200,
                'data' => null,
            ]);
    }

    /**
     * Test 11: Subscription starting tomorrow (started_on = tomorrow) returns null.
     */
    public function test_subscription_starting_tomorrow_returns_null(): void
    {
        $plan = Subscription::create(['name' => 'Tomorrow Plan', 'price' => 9.99]);

        UserSubscription::create([
            'user_id' => $this->user->id,
            'subscription_id' => $plan->id,
            'status' => 1,
            'started_on' => Carbon::tomorrow()->toDateString(),
            'ended_at' => Carbon::tomorrow()->addMonth()->toDateString(),
        ]);

        Passport::actingAs($this->user);
        $response = $this->getJson('/api/my-current-subscription');

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'response_code' => 200,
                'data' => null,
            ]);
    }
}
