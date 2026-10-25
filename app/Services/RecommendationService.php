<?php

namespace App\Services;

use App\Models\ArtistFollower;
use App\Models\Genre;
use App\Models\PlayHistory;
use App\Models\Song;
use App\Models\SongLike;
use App\Models\UserPreference;
use Illuminate\Database\Eloquent\Builder;

class RecommendationService
{
    /**
     * Resolve a deterministic seed for dynamic pseudo-random ordering.
     * Within the same user session / hour, the seed stays constant so that:
     * - Dashboard (initial 5 items)
     * - Section Details ("See All" pagination)
     * - Player Queue
     * all return identical tracks in the exact same sequence.
     *
     * @param mixed $seed
     * @param int|null $userId
     * @return int
     */
    public static function resolveSeed($seed = null, ?int $userId = null): int
    {
        if ($seed !== null && $seed !== '') {
            return is_numeric($seed) ? (int)$seed : abs(crc32((string)$seed));
        }

        $userKey = ($userId && $userId > 0) ? "user_{$userId}" : ('ip_' . (request()->ip() ?? 'guest'));
        $timeKey = date('YmdH'); // Hourly rotating seed for freshness
        return abs(crc32("{$userKey}_{$timeKey}"));
    }

    /**
     * Build the user's Spotify-like taste profile from multiple signals:
     * - Followed & preferred artists
     * - Artists of liked songs & played songs
     * - Top listened genres
     * - Liked songs and played songs
     *
     * @param int|null $userId
     * @return array
     */
    public static function getUserTasteProfile(?int $userId): array
    {
        if (!$userId || $userId <= 0) {
            return [
                'affinity_artist_ids' => [],
                'top_genre_ids' => [],
                'liked_song_ids' => [],
                'played_song_ids' => [],
            ];
        }

        // 1. Followed & preferred artists
        $followedArtistIds = ArtistFollower::where('user_id', $userId)->pluck('artist_id')->toArray();
        $preferredArtistIds = UserPreference::where('user_id', $userId)->pluck('artist_id')->toArray();

        // 2. Liked songs
        $likedSongIds = SongLike::where('user_id', $userId)->pluck('song_id')->toArray();
        $likedSongArtistIds = !empty($likedSongIds)
            ? Song::whereIn('id', $likedSongIds)->pluck('user_id')->filter()->unique()->toArray()
            : [];
        $likedGenreIds = !empty($likedSongIds)
            ? Song::whereIn('id', $likedSongIds)->pluck('genre_id')->filter()->unique()->toArray()
            : [];

        // 3. Play History (last 50 songs)
        $playHistory = PlayHistory::with('song:id,genre_id,user_id')
            ->where('user_id', $userId)
            ->orderByDesc('last_played_at')
            ->take(50)
            ->get();

        $playedSongIds = $playHistory->pluck('song_id')->filter()->unique()->toArray();
        $historyArtistIds = $playHistory->pluck('song.user_id')->filter()->unique()->toArray();

        // Rank genres by listening frequency
        $historyGenreCounts = [];
        foreach ($playHistory as $item) {
            if ($item->song && $item->song->genre_id) {
                $gid = $item->song->genre_id;
                $historyGenreCounts[$gid] = ($historyGenreCounts[$gid] ?? 0) + 1;
            }
        }
        arsort($historyGenreCounts);
        $historyTopGenreIds = array_keys($historyGenreCounts);

        $topGenreIds = array_values(array_unique(array_merge($historyTopGenreIds, $likedGenreIds)));
        $affinityArtistIds = array_values(array_unique(array_merge(
            $followedArtistIds,
            $preferredArtistIds,
            $likedSongArtistIds,
            $historyArtistIds
        )));

        return [
            'affinity_artist_ids' => $affinityArtistIds,
            'top_genre_ids' => $topGenreIds,
            'liked_song_ids' => $likedSongIds,
            'played_song_ids' => $playedSongIds,
        ];
    }

    /**
     * Dynamic genre suggestions query (e.g. "Punta for you", "Rock for you").
     * Personalized if active user has taste in this genre, or dynamically randomized for cold-start.
     *
     * @param int $genreId
     * @param int|null $userId
     * @param int|null $seed
     * @return Builder
     */
    public static function getGenreForYouQuery(int $genreId, ?int $userId = null, ?int $seed = null): Builder
    {
        $seedInt = self::resolveSeed($seed, $userId);
        $query = Song::with(['artist', 'album', 'genre'])
            ->where('status', 1)
            ->where('genre_id', $genreId);

        if ($userId && $userId > 0) {
            $taste = self::getUserTasteProfile($userId);

            // Filter taste components specific to this genre
            $genreLikedSongIds = !empty($taste['liked_song_ids'])
                ? Song::where('genre_id', $genreId)->whereIn('id', $taste['liked_song_ids'])->pluck('id')->toArray()
                : [];
            $genreArtistIds = !empty($taste['affinity_artist_ids'])
                ? Song::where('genre_id', $genreId)->whereIn('user_id', $taste['affinity_artist_ids'])->pluck('user_id')->unique()->toArray()
                : [];
            $genrePlayedSongIds = !empty($taste['played_song_ids'])
                ? Song::where('genre_id', $genreId)->whereIn('id', $taste['played_song_ids'])->pluck('id')->toArray()
                : [];

            $hasLiked = !empty($genreLikedSongIds);
            $hasArtists = !empty($genreArtistIds);
            $hasPlayed = !empty($genrePlayedSongIds);

            if ($hasLiked || $hasArtists || $hasPlayed) {
                $likedList = $hasLiked ? implode(',', array_map('intval', $genreLikedSongIds)) : '0';
                $artistList = $hasArtists ? implode(',', array_map('intval', $genreArtistIds)) : '0';
                $playedList = $hasPlayed ? implode(',', array_map('intval', $genrePlayedSongIds)) : '0';

                // Spotify affinity scoring:
                // +50 for user-liked tracks in this genre
                // +30 for tracks from followed/preferred artists in this genre
                // +20 for previously played tracks in this genre
                $scoreRaw = "(
                    (CASE WHEN id IN ({$likedList}) THEN 50 ELSE 0 END) +
                    (CASE WHEN user_id IN ({$artistList}) THEN 30 ELSE 0 END) +
                    (CASE WHEN id IN ({$playedList}) THEN 20 ELSE 0 END)
                )";

                return $query->orderByRaw("{$scoreRaw} DESC")
                    ->inRandomOrder($seedInt)
                    ->orderBy('id', 'asc');
            }
        }

        // Cold-start / Guest user: dynamically random within this genre!
        return $query->inRandomOrder($seedInt)->orderBy('id', 'asc');
    }

    /**
     * Spotify-style "Made for you" recommendation query.
     * Blends songs from affinity artists, top genres, and liked tracks,
     * with cold-start dynamic exploration fallback.
     *
     * @param int|null $userId
     * @param int|null $seed
     * @return Builder
     */
    public static function getMadeForYouQuery(?int $userId = null, ?int $seed = null): Builder
    {
        $seedInt = self::resolveSeed($seed, $userId);
        $query = Song::with(['artist', 'album', 'genre'])
            ->where('status', 1);

        if ($userId && $userId > 0) {
            $taste = self::getUserTasteProfile($userId);

            $hasLiked = !empty($taste['liked_song_ids']);
            $hasArtists = !empty($taste['affinity_artist_ids']);
            $hasGenres = !empty($taste['top_genre_ids']);
            $hasPlayed = !empty($taste['played_song_ids']);

            if ($hasLiked || $hasArtists || $hasGenres || $hasPlayed) {
                $likedList = $hasLiked ? implode(',', array_map('intval', $taste['liked_song_ids'])) : '0';
                $artistList = $hasArtists ? implode(',', array_map('intval', $taste['affinity_artist_ids'])) : '0';
                $genreList = $hasGenres ? implode(',', array_map('intval', $taste['top_genre_ids'])) : '0';
                $playedList = $hasPlayed ? implode(',', array_map('intval', $taste['played_song_ids'])) : '0';

                // Holistic affinity score:
                // +50 liked songs
                // +35 followed / preferred / favorite artists
                // +25 user's top listened genres
                // +15 recently played songs
                $scoreRaw = "(
                    (CASE WHEN id IN ({$likedList}) THEN 50 ELSE 0 END) +
                    (CASE WHEN user_id IN ({$artistList}) THEN 35 ELSE 0 END) +
                    (CASE WHEN genre_id IN ({$genreList}) THEN 25 ELSE 0 END) +
                    (CASE WHEN id IN ({$playedList}) THEN 15 ELSE 0 END)
                )";

                return $query->orderByRaw("{$scoreRaw} DESC")
                    ->inRandomOrder($seedInt)
                    ->orderBy('id', 'asc');
            }
        }

        // Cold-start / Guest user:
        // Rank songs with play counts slightly higher, dynamically randomized by seed
        $coldScore = "(CASE WHEN play_count > 0 THEN 10 ELSE 0 END)";
        return $query->orderByRaw("{$coldScore} DESC")
            ->inRandomOrder($seedInt)
            ->orderBy('id', 'asc');
    }

    /**
     * "Features songs" query.
     * Curates high-engagement and trending songs, personalized towards user's preferred
     * genres and artists if available, dynamically randomized.
     *
     * @param int|null $userId
     * @param int|null $seed
     * @return Builder
     */
    public static function getFeaturedSongsQuery(?int $userId = null, ?int $seed = null): Builder
    {
        $seedInt = self::resolveSeed($seed, $userId);
        $query = Song::with(['artist', 'album', 'genre'])
            ->where('status', 1);

        if ($userId && $userId > 0) {
            $taste = self::getUserTasteProfile($userId);
            $hasArtists = !empty($taste['affinity_artist_ids']);
            $hasGenres = !empty($taste['top_genre_ids']);

            if ($hasArtists || $hasGenres) {
                $artistList = $hasArtists ? implode(',', array_map('intval', $taste['affinity_artist_ids'])) : '0';
                $genreList = $hasGenres ? implode(',', array_map('intval', $taste['top_genre_ids'])) : '0';

                $featureScore = "(
                    (CASE WHEN user_id IN ({$artistList}) THEN 25 ELSE 0 END) +
                    (CASE WHEN genre_id IN ({$genreList}) THEN 20 ELSE 0 END) +
                    (CASE WHEN play_count > 100 THEN 25 WHEN play_count > 10 THEN 15 WHEN play_count > 0 THEN 5 ELSE 0 END) +
                    (CASE WHEN likes_count > 50 THEN 20 WHEN likes_count > 5 THEN 10 ELSE 0 END)
                )";

                return $query->orderByRaw("{$featureScore} DESC")
                    ->inRandomOrder($seedInt)
                    ->orderBy('id', 'asc');
            }
        }

        // Cold-start / Guest user:
        $coldFeatureScore = "(
            (CASE WHEN play_count > 100 THEN 30 WHEN play_count > 10 THEN 15 WHEN play_count > 0 THEN 5 ELSE 0 END) +
            (CASE WHEN likes_count > 50 THEN 25 WHEN likes_count > 5 THEN 10 ELSE 0 END)
        )";

        return $query->orderByRaw("{$coldFeatureScore} DESC")
            ->inRandomOrder($seedInt)
            ->orderBy('id', 'asc');
    }
}
