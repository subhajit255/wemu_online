<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AudienceLog>
 */
class AudienceLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $countries = ['United States', 'India', 'Indonesia', 'Brazil', 'Germany', 'United Kingdom', 'Canada', 'Australia'];
        return [
            'ip_address' => $this->faker->ipv4,
            'country' => $this->faker->randomElement($countries),
            'city' => $this->faker->city,
            'region' => $this->faker->state,
            'latitude' => $this->faker->latitude,
            'longitude' => $this->faker->longitude,
            'user_id' => null,
            'artist_id' => 1, // Assume artist ID 1 for testing
            'album_id' => null,
            'song_id' => null,
            'user_agent' => $this->faker->userAgent,
            'url' => $this->faker->url,
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => now(),
        ];
    }
}
