<?php
$artists = \App\Models\User::where('user_type', 3)->pluck('id');
$userCounter = time(); 

foreach ($artists as $artistId) {
    // Generate stream logs
    for ($i = 0; $i < 12; $i++) {
        $date = now()->subWeeks($i);
        $count = rand(5, 20);
        for ($j = 0; $j < $count; $j++) {
            \App\Models\StreamLog::create([
                'artist_id' => $artistId,
                'song_id' => 1,
                'user_id' => 1,
                'created_at' => $date->copy()->startOfWeek()->addDays(rand(0, 6)),
                'updated_at' => $date->copy()->startOfWeek()->addDays(rand(0, 6)),
            ]);
        }
    }

    for ($i = 0; $i < 12; $i++) {
        $date = now()->subWeeks($i);
        // This year
        $count = rand(5, 15); 
        for ($j = 0; $j < $count; $j++) {
            $userCounter++;
            $user = \App\Models\User::create([
                'name' => 'D' . $userCounter,
                'email' => 'd' . $userCounter . '@t.com',
                'password' => bcrypt('pass'),
                'user_type' => 1,
                'mobile_number' => '5' . $userCounter,
            ]);
            
            \App\Models\ArtistFollower::create([
                'artist_id' => $artistId,
                'user_id' => $user->id,
                'created_at' => $date->copy()->startOfWeek()->addDays(rand(0, 6)),
                'updated_at' => $date->copy()->startOfWeek()->addDays(rand(0, 6)),
            ]);
        }
        
        // Last year
        $dateLY = $date->copy()->subYear();
        $countLY = rand(1, 10);
        for ($j = 0; $j < $countLY; $j++) {
            $userCounter++;
            $user = \App\Models\User::create([
                'name' => 'D' . $userCounter,
                'email' => 'd' . $userCounter . '@t.com',
                'password' => bcrypt('pass'),
                'user_type' => 1,
                'mobile_number' => '5' . $userCounter,
            ]);

            \App\Models\ArtistFollower::create([
                'artist_id' => $artistId,
                'user_id' => $user->id,
                'created_at' => $dateLY->copy()->startOfWeek()->addDays(rand(0, 6)),
                'updated_at' => $dateLY->copy()->startOfWeek()->addDays(rand(0, 6)),
            ]);
        }
    }
}
echo "Dummy followers and streams inserted successfully for all artists.\n";
