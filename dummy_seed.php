<?php

$artistId = \App\Models\User::where('user_type', 3)->first()->id ?? 3;

$userCounter = time(); // random starting point

for ($i = 0; $i < 12; $i++) {
    // This year data
    $date = now()->subWeeks($i);
    $count = rand(5, 50);
    for ($j = 0; $j < $count; $j++) {
        $userCounter++;
        $user = \App\Models\User::create([
            'name' => 'Dummy Follower ' . $userCounter,
            'email' => 'dummy' . $userCounter . '@test.com',
            'password' => bcrypt('password'),
            'user_type' => 1,
            'mobile_number' => '555' . $userCounter,
        ]);
        
        \App\Models\ArtistFollower::create([
            'artist_id' => $artistId,
            'user_id' => $user->id,
            'created_at' => $date->copy()->startOfWeek()->addDays(rand(0, 6)),
            'updated_at' => $date->copy()->startOfWeek()->addDays(rand(0, 6)),
        ]);
    }
    
    // Last year data
    $dateLY = $date->copy()->subYear();
    $countLY = rand(2, 35);
    for ($j = 0; $j < $countLY; $j++) {
        $userCounter++;
        $user = \App\Models\User::create([
            'name' => 'Dummy Follower ' . $userCounter,
            'email' => 'dummy' . $userCounter . '@test.com',
            'password' => bcrypt('password'),
            'user_type' => 1,
            'mobile_number' => '555' . $userCounter,
        ]);

        \App\Models\ArtistFollower::create([
            'artist_id' => $artistId,
            'user_id' => $user->id,
            'created_at' => $dateLY->copy()->startOfWeek()->addDays(rand(0, 6)),
            'updated_at' => $dateLY->copy()->startOfWeek()->addDays(rand(0, 6)),
        ]);
    }
}
echo "Dummy followers inserted successfully.\n";
