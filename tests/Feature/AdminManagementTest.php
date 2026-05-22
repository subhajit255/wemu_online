<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class AdminManagementTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        // Bypass all permission/gate checks during the test
        Gate::before(function () {
            return true;
        });
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
    }

    /**
     * Test creating a customer and verifying subscription_type.
     */
    public function test_customer_creation_and_subscription_type(): void
    {
        // 1. Create and authenticate an admin user
        $admin = User::factory()->create([
            'user_type' => 1, // Admin
        ]);
        $this->actingAs($admin);

        // 2. Add a new customer user with family subscription
        $customerData = [
            'name' => 'John Customer',
            'username' => 'johncustomer',
            'mobile_number' => '1234567890',
            'email' => 'john.customer@example.com',
            'subscription_type' => 'family',
        ];

        $response = $this->post(route('admin.user.add'), $customerData);

        // 3. Verify successful response structure
        $response->assertStatus(200);
        $response->assertJson([
            'status' => true,
            'message' => 'User Created Successfully',
        ]);

        // 4. Query the DB and assert correctly saved fields
        $user = User::where('email', 'john.customer@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals(4, $user->user_type); // Must be customer (4)
        $this->assertEquals('family', $user->subscription_type); // Real subscription type
    }

    /**
     * Test updating a customer.
     */
    public function test_customer_update(): void
    {
        $admin = User::factory()->create(['user_type' => 1]);
        $this->actingAs($admin);

        // Create a customer
        $customer = User::factory()->create([
            'user_type' => 4,
            'subscription_type' => 'individual',
        ]);

        $updateData = [
            'id' => $customer->id,
            'name' => 'John Updated',
            'mobile_number' => '9876543210',
            'email' => 'john.updated@example.com',
            'subscription_type' => 'duo',
        ];

        $response = $this->post(route('admin.user.add'), $updateData);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => true,
            'message' => 'User Updated Successfully',
        ]);

        $customer->refresh();
        $this->assertEquals('John Updated', $customer->name);
        $this->assertEquals('duo', $customer->subscription_type);
        $this->assertEquals(4, $customer->user_type);
    }

    /**
     * Test customer listing.
     */
    public function test_customer_list_view(): void
    {
        $admin = User::factory()->create(['user_type' => 1]);
        $this->actingAs($admin);

        // Create individual, duo, family users
        User::factory()->create(['user_type' => 4, 'subscription_type' => 'individual']);
        User::factory()->create(['user_type' => 4, 'subscription_type' => 'duo']);
        User::factory()->create(['user_type' => 4, 'subscription_type' => 'family']);

        $response = $this->get(route('admin.user.list'));
        $response->assertStatus(200);
        
        // Assert view contains subscription pills and is missing old columns
        $response->assertSee('Individual');
        $response->assertSee('Duo');
        $response->assertSee('Family');
        $response->assertDontSee('Top Genre');
        $response->assertDontSee('Stream Hours');
    }

    /**
     * Test creating an artist and verifying they are user_type = 3.
     */
    public function test_artist_creation(): void
    {
        $admin = User::factory()->create(['user_type' => 1]);
        $this->actingAs($admin);

        $artistData = [
            'name' => 'Elton Artist',
            'username' => 'eltonartist',
            'mobile_number' => '5556667777',
            'email' => 'elton.artist@example.com',
        ];

        $response = $this->post(route('admin.artist.add'), $artistData);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => true,
            'message' => 'Artist Created Successfully',
        ]);

        $artist = User::where('email', 'elton.artist@example.com')->first();
        $this->assertNotNull($artist);
        $this->assertEquals(3, $artist->user_type); // Must be artist (3)
        // Subscription type should default to 'individual' or null, but artists do not use it
    }

    /**
     * Test editing an artist.
     */
    public function test_artist_edit(): void
    {
        $admin = User::factory()->create(['user_type' => 1]);
        $this->actingAs($admin);

        $artist = User::factory()->create([
            'user_type' => 3,
            'name' => 'Original Artist',
        ]);

        $updateData = [
            'id' => $artist->id,
            'name' => 'Elton Updated',
            'mobile_number' => '1112223333',
            'email' => 'elton.updated@example.com',
        ];

        $response = $this->post(route('admin.artist.add'), $updateData);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => true,
            'message' => 'Artist Updated Successfully',
        ]);

        $artist->refresh();
        $this->assertEquals('Elton Updated', $artist->name);
        $this->assertEquals(3, $artist->user_type);
    }

    /**
     * Test artist list view.
     */
    public function test_artist_list_view(): void
    {
        $admin = User::factory()->create(['user_type' => 1]);
        $this->actingAs($admin);

        User::factory()->create([
            'user_type' => 3,
            'name' => 'Superstar Artist',
        ]);

        $response = $this->get(route('admin.artist.list'));
        $response->assertStatus(200);
        $response->assertSee('Superstar Artist');
    }
}
