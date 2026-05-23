<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DefaultSubscriptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Subscription::updateOrCreate(
            ['slug' => 'free'],
            [
                'name' => 'Free',
                'available_for' => 1,
                'interval' => 1,
                'price' => 0.00,
                'currency' => 'USD',
                'description' => 'A basic free tier for all users.',
                'features' => '<ul><li>Stream music with ads</li><li>Limited song skips</li><li>Standard audio quality</li><li>Create and share playlists</li><li>Discover new artists and albums</li><li>Access on mobile, desktop, and web</li><li>Shuffle play on selected devices</li><li>Follow artists and friends</li><li>Daily mixes and recommendations</li></ul>',
                'status' => 1,
                'is_default' => 1,
            ]
        );
    }
}
