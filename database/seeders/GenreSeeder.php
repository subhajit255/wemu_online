<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GenreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $genres = [
            'Pop',
            'Rock',
            'Hip Hop',
            'R&B',
            'Electronic',
            'Jazz',
            'Classical',
            'Country',
            'Reggae',
            'Folk'
        ];

        foreach ($genres as $genre) {
            \Illuminate\Support\Facades\DB::table('genres')->updateOrInsert(
                ['slug' => \Illuminate\Support\Str::slug($genre)],
                [
                    'title' => $genre,
                    'description' => $genre . ' music genre',
                    'is_active' => 1,
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );
        }
    }
}
