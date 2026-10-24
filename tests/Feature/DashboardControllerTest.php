<?php

namespace Tests\Feature;

use App\Models\ArtistFollower;
use App\Models\Genre;
use App\Models\PlayHistory;
use App\Models\Song;
use App\Models\SongLike;
use App\Models\User;
use App\Models\UserPreference;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Http;
use Laravel\Passport\Passport;
use Tests\TestCase;

class DashboardControllerTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        Http::fake([
            'ip-api.com/*' => Http::response(['status' => 'success', 'country' => 'California'], 200),
        ]);
    }

    /**
     * Test cold-start new user: 0 likes, 0 history, 0 artist preferences.
     * Dashboard and Section Details must have identical top 5 songs ordered by popularity.
     */
    public function test_cold_start_new_user_dashboard_and_section_details_consistency(): void
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        // Create 2 genres with published songs
        $rock = Genre::create(['title' => 'Rock', 'slug' => 'rock', 'is_active' => 1]);
        $pop = Genre::create(['title' => 'Pop', 'slug' => 'pop', 'is_active' => 1]);

        // Create 8 rock songs with different play counts
        $songIdsInExpectedOrder = [];
        $playCounts = [100, 80, 60, 40, 20, 10, 5, 2];
        foreach ($playCounts as $i => $plays) {
            $song = Song::create([
                'title' => "Rock Song $i",
                'slug' => "rock-song-$i",
                'genre_id' => $rock->id,
                'status' => 1,
                'play_count' => $plays,
                'likes_count' => 0,
                'published_at' => now()->subDays($i),
            ]);
            $songIdsInExpectedOrder[] = $song->id;
        }

        // Create 2 pop songs
        Song::create([
            'title' => "Pop Song 1",
            'slug' => "pop-song-1",
            'genre_id' => $pop->id,
            'status' => 1,
            'play_count' => 50,
        ]);

        // 1. Hit Dashboard API
        $response = $this->getJson(route('user.dashboard'));
        $response->assertStatus(200);
        $response->assertJson(['status' => true]);

        $sections = $response->json('data.sections');
        $this->assertNotEmpty($sections);

        // Find the 'Rock for you' section
        $rockSection = collect($sections)->first(function ($s) {
            return $s['type_id'] === 'rock-for-you';
        });

        $this->assertNotNull($rockSection, "Rock for you section should be present on dashboard");
        $this->assertCount(5, $rockSection['items'], "Dashboard should return exactly 5 songs");

        // Top 5 songs on dashboard should match the top 5 highest play counts
        $dashboardSongIds = collect($rockSection['items'])->pluck('id')->toArray();
        $expectedTop5 = array_slice($songIdsInExpectedOrder, 0, 5);
        $this->assertEquals($expectedTop5, $dashboardSongIds);

        // 2. Hit Section Details API for 'rock-for-you'
        $detailsResponse = $this->getJson(route('user.dashboard.sectionDetails', ['type_id' => 'rock-for-you', 'per_page' => 15]));
        $detailsResponse->assertStatus(200);
        $detailsResponse->assertJson(['status' => true]);

        $detailsItems = $detailsResponse->json('data.result');
        $this->assertCount(8, $detailsItems, "Section details should return all 8 rock songs");

        $detailsSongIds = collect($detailsItems)->pluck('id')->toArray();

        // The first 5 songs of Section Details MUST match the 5 songs from Dashboard
        $detailsFirst5 = array_slice($detailsSongIds, 0, 5);
        $this->assertEquals($dashboardSongIds, $detailsFirst5, "Section details first 5 songs must be identical to Dashboard");

        // The remaining songs must continue in expected popularity order
        $this->assertEquals($songIdsInExpectedOrder, $detailsSongIds);

        // Pagination metadata assertions
        $pagination = $detailsResponse->json('data.pagination');
        $this->assertEquals(8, $pagination['total']);
        $this->assertEquals(15, $pagination['per_page']);
        $this->assertEquals(1, $pagination['current_page']);
    }

    /**
     * Test active user with liked songs in that genre gets +50 boost.
     */
    public function test_active_user_with_liked_songs_gets_boosted(): void
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        $jazz = Genre::create(['title' => 'Jazz', 'slug' => 'jazz', 'is_active' => 1]);

        // Song A has low plays
        $songA = Song::create([
            'title' => 'Underground Jazz',
            'slug' => 'underground-jazz',
            'genre_id' => $jazz->id,
            'status' => 1,
            'play_count' => 5,
            'likes_count' => 1,
            'published_at' => now(),
        ]);

        // Songs B-D have high plays
        $songB = Song::create(['title' => 'Jazz Hit 1', 'genre_id' => $jazz->id, 'status' => 1, 'play_count' => 100, 'published_at' => now()]);
        $songC = Song::create(['title' => 'Jazz Hit 2', 'genre_id' => $jazz->id, 'status' => 1, 'play_count' => 80, 'published_at' => now()]);
        $songD = Song::create(['title' => 'Jazz Hit 3', 'genre_id' => $jazz->id, 'status' => 1, 'play_count' => 60, 'published_at' => now()]);

        // User likes Song A
        SongLike::create(['user_id' => $user->id, 'song_id' => $songA->id]);

        $response = $this->getJson(route('user.dashboard.sectionDetails', ['type_id' => 'jazz-for-you']));
        $response->assertStatus(200);

        $results = $response->json('data.result');
        $this->assertEquals($songA->id, $results[0]['id'], "Liked song should be ranked #1 due to affinity boost");
        $this->assertEquals($songB->id, $results[1]['id']);
    }

    /**
     * Test active user with followed artists gets +30 boost.
     */
    public function test_active_user_with_followed_artist_gets_boosted(): void
    {
        $user = User::factory()->create();
        $favoriteArtist = User::factory()->create();
        Passport::actingAs($user);

        // User follows artist
        ArtistFollower::create(['user_id' => $user->id, 'artist_id' => $favoriteArtist->id]);

        $hiphop = Genre::create(['title' => 'Hip Hop', 'slug' => 'hip-hop', 'is_active' => 1]);

        // Song by followed artist (low plays)
        $artistSong = Song::create([
            'title' => 'Favorite Artist Track',
            'user_id' => $favoriteArtist->id,
            'genre_id' => $hiphop->id,
            'status' => 1,
            'play_count' => 10,
            'published_at' => now(),
        ]);

        // Other artist song (high plays)
        $otherSong = Song::create([
            'title' => 'Other Artist Track',
            'user_id' => null,
            'genre_id' => $hiphop->id,
            'status' => 1,
            'play_count' => 200,
            'published_at' => now(),
        ]);

        $response = $this->getJson(route('user.dashboard.sectionDetails', ['type_id' => 'hip-hop-for-you']));
        $response->assertStatus(200);

        $results = $response->json('data.result');
        $this->assertEquals($artistSong->id, $results[0]['id'], "Followed artist track should be boosted above other tracks");
    }

    /**
     * Test active user with play history in that genre gets +20 boost.
     */
    public function test_active_user_with_play_history_gets_boosted(): void
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        $edm = Genre::create(['title' => 'EDM', 'slug' => 'edm', 'is_active' => 1]);

        // Song X (low plays, but played by user)
        $songX = Song::create(['title' => 'Played Track', 'genre_id' => $edm->id, 'status' => 1, 'play_count' => 10, 'published_at' => now()]);
        // Song Y (higher plays, never played by user)
        $songY = Song::create(['title' => 'Unplayed Track', 'genre_id' => $edm->id, 'status' => 1, 'play_count' => 50, 'published_at' => now()]);

        PlayHistory::create([
            'user_id' => $user->id,
            'song_id' => $songX->id,
            'play_count' => 5,
            'last_played_at' => now(),
        ]);

        $response = $this->getJson(route('user.dashboard.sectionDetails', ['type_id' => 'edm-for-you']));
        $response->assertStatus(200);

        $results = $response->json('data.result');
        $this->assertEquals($songX->id, $results[0]['id'], "Previously played track should be boosted");
        $this->assertEquals($songY->id, $results[1]['id']);
    }

    /**
     * Test pagination consistency across page 1 and page 2.
     */
    public function test_section_details_pagination_page_1_and_page_2(): void
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        $classical = Genre::create(['title' => 'Classical', 'slug' => 'classical', 'is_active' => 1]);

        for ($i = 1; $i <= 10; $i++) {
            Song::create([
                'title' => "Classical Opus $i",
                'genre_id' => $classical->id,
                'status' => 1,
                'play_count' => 100 - $i,
                'published_at' => now(),
            ]);
        }

        // Page 1 (per_page = 5)
        $page1Response = $this->getJson(route('user.dashboard.sectionDetails', [
            'type_id' => 'classical-for-you',
            'per_page' => 5,
            'page' => 1,
        ]));
        $page1Response->assertStatus(200);
        $page1Ids = collect($page1Response->json('data.result'))->pluck('id')->toArray();
        $this->assertCount(5, $page1Ids);

        // Page 2 (per_page = 5)
        $page2Response = $this->getJson(route('user.dashboard.sectionDetails', [
            'type_id' => 'classical-for-you',
            'per_page' => 5,
            'page' => 2,
        ]));
        $page2Response->assertStatus(200);
        $page2Ids = collect($page2Response->json('data.result'))->pluck('id')->toArray();
        $this->assertCount(5, $page2Ids);

        // Assert zero overlap between page 1 and page 2
        $overlap = array_intersect($page1Ids, $page2Ids);
        $this->assertEmpty($overlap, "Page 1 and Page 2 should not have overlapping songs");
    }

    /**
     * Test validation and error handling.
     */
    public function test_missing_type_id_returns_422(): void
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        $response = $this->getJson(route('user.dashboard.sectionDetails'));
        $response->assertStatus(422);
    }

    public function test_unknown_section_returns_404(): void
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        $response = $this->getJson(route('user.dashboard.sectionDetails', ['type_id' => 'non-existent-section']));
        $response->assertStatus(404);
        $response->assertJson(['status' => false, 'message' => 'Section not found']);
    }

    /**
     * Test that non-dynamic sections remain functional.
     */
    public function test_standard_sections_remain_functional(): void
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        Song::create(['title' => 'Standard Song', 'status' => 1, 'play_count' => 10, 'published_at' => now()]);

        // 'made-for-you' section
        $response1 = $this->getJson(route('user.dashboard.sectionDetails', ['type_id' => 'made-for-you']));
        $response1->assertStatus(200);
        $response1->assertJson(['status' => true]);

        // 'new-release' section
        $response2 = $this->getJson(route('user.dashboard.sectionDetails', ['type_id' => 'new-release']));
        $response2->assertStatus(200);
        $response2->assertJson(['status' => true]);
    }

    /**
     * Test that Dashboard, Section Details, and Player Queue are 100% in sync for dynamic genre songs.
     */
    public function test_player_queue_is_synchronized_with_dashboard_and_section_details(): void
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        $rock = Genre::create(['title' => 'Rock Hits', 'slug' => 'rock-hits', 'is_active' => 1]);

        $createdSongs = [];
        $playCounts = [500, 400, 300, 200, 100, 50, 25];
        foreach ($playCounts as $i => $plays) {
            $createdSongs[] = Song::create([
                'title' => "Rock Track " . ($i + 1),
                'genre_id' => $rock->id,
                'status' => 1,
                'play_count' => $plays,
                'published_at' => now()->subDays($i),
            ]);
        }
        $expectedSongIds = collect($createdSongs)->pluck('id')->toArray();

        // 1. Dashboard: returns top 5 songs
        $dashboardResponse = $this->getJson(route('user.dashboard'));
        $dashboardResponse->assertStatus(200);
        $sections = $dashboardResponse->json('data.sections');
        $rockSection = collect($sections)->first(fn($s) => $s['type_id'] === 'rock-hits-for-you');
        $this->assertNotNull($rockSection);
        $dashboardTop5Ids = collect($rockSection['items'])->pluck('id')->toArray();
        $this->assertEquals(array_slice($expectedSongIds, 0, 5), $dashboardTop5Ids);

        // 2. Section Details (See all): returns first 5 songs as it is, followed by 6..N
        $detailsResponse = $this->getJson(route('user.dashboard.sectionDetails', ['type_id' => 'rock-hits-for-you', 'per_page' => 15]));
        $detailsResponse->assertStatus(200);
        $detailsResultIds = collect($detailsResponse->json('data.result'))->pluck('id')->toArray();
        $this->assertEquals($expectedSongIds, $detailsResultIds);

        // 3. Player Queue: returns exact same result array and proper local_queue
        // A) Initial queue without last_played_song_id
        $queueResponse = $this->getJson(route('player.queue', ['source_type' => 'rock-hits-for-you', 'per_page' => 15]));
        $queueResponse->assertStatus(200);
        $queueResultIds = collect($queueResponse->json('data.result'))->pluck('id')->toArray();
        $this->assertEquals($expectedSongIds, $queueResultIds, "Player queue result must match Section Details and Dashboard");

        // B) User plays song 3 (last_played_song_id = expectedSongIds[2]) with direction = next
        $playingSongId = $expectedSongIds[2]; // 3rd song
        $queueNextResponse = $this->getJson(route('player.queue', [
            'source_type' => 'rock-hits-for-you',
            'last_played_song_id' => $playingSongId,
            'direction' => 'next',
            'per_page' => 15,
        ]));
        $queueNextResponse->assertStatus(200);

        // The local_queue must contain songs after song 3: [song 4, song 5, song 6, song 7]
        $localQueueIds = collect($queueNextResponse->json('data.local_queue'))->pluck('id')->toArray();
        $expectedNextQueue = array_slice($expectedSongIds, 3); // from index 3 onwards (songs 4, 5, 6, 7)
        $this->assertEquals($expectedNextQueue, $localQueueIds, "Local queue must seamlessly play the next songs in order");
    }
}
