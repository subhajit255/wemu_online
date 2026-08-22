<?php

namespace App\Docs;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="WEMU API Documentation",
 *      description="Swagger documentation for all APIs"
 * )
 *
 * @OA\Server(
 *      url=L5_SWAGGER_CONST_HOST,
 *      description="API Server"
 * )
 *
 * @OA\SecurityScheme(
 *      securityScheme="bearerAuth",
 *      type="http",
 *      scheme="bearer"
 * )
 */
class SwaggerDef
{
    /**
     * @OA\Get(
     *      path="/api/albums",
     *      operationId="albums",
     *      tags={"Master"},
     *      summary="Endpoint for api/albums",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *       )
     * )
     */
    public function albums(){}

    /**
     * @OA\Get(
     *      path="/api/artist/analytics/streams",
     *      operationId="artist.analytics.streams",
     *      tags={"Analytics"},
     *      summary="Endpoint for api/artist/analytics/streams",
     *      security={{"bearerAuth":{}}},
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *       )
     * )
     */
    public function artist_analytics_streams(){}

    /**
     * @OA\Get(
     *      path="/api/artist/details/{id}",
     *      operationId="artist.details",
     *      tags={"Artist"},
     *      summary="Endpoint for api/artist/details/{id}",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *       )
     * )
     */
    public function artist_details(){}

    /**
     * @OA\Get(
     *      path="/api/artists",
     *      operationId="artists",
     *      tags={"Artist"},
     *      summary="Endpoint for api/artists",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *       )
     * )
     */
    public function artists(){}

    /**
     * @OA\Get(
     *      path="/api/biggest-hits",
     *      operationId="biggest.hits",
     *      tags={"Song"},
     *      summary="Endpoint for api/biggest-hits",
     *      security={{"bearerAuth":{}}},
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *       )
     * )
     */
    public function biggest_hits(){}

    /**
     * @OA\Post(
     *      path="/api/category/list",
     *      operationId="category.list",
     *      tags={"Auth"},
     *      summary="Endpoint for api/category/list",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *       )
     * )
     */
    public function category_list(){}

    /**
     * @OA\Post(
     *      path="/api/change/password",
     *      operationId="change.password",
     *      tags={"Auth"},
     *      summary="Endpoint for api/change/password",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *       )
     * )
     */
    public function change_password(){}

    /**
     * @OA\Post(
     *      path="/api/change/pin",
     *      operationId="change.pin",
     *      tags={"Auth"},
     *      summary="Endpoint for api/change/pin",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *       )
     * )
     */
    public function change_pin(){}

    /**
     * @OA\Get(
     *      path="/api/cms",
     *      operationId="cms",
     *      tags={"Auth"},
     *      summary="Endpoint for api/cms",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *       )
     * )
     */
    public function cms(){}

    /**
     * @OA\Post(
     *      path="/api/contact-us",
     *      operationId="contact.us",
     *      tags={"Auth"},
     *      summary="Endpoint for api/contact-us",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *       )
     * )
     */
    public function contact_us(){}

    /**
     * @OA\Get(
     *      path="/api/dashboard",
     *      operationId="user.dashboard",
     *      tags={"Dashboard"},
     *      summary="Endpoint for api/dashboard",
     *      security={{"bearerAuth":{}}},
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *       )
     * )
     */
    public function user_dashboard(){}

    /**
     * @OA\Get(
     *      path="/api/dashboard/section-details",
     *      operationId="user.dashboard.sectionDetails",
     *      tags={"Dashboard"},
     *      summary="Endpoint for api/dashboard/section-details",
     *      security={{"bearerAuth":{}}},
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *       )
     * )
     */
    public function user_dashboard_sectionDetails(){}

    /**
     * @OA\Get(
     *      path="/api/faq",
     *      operationId="faq",
     *      tags={"Auth"},
     *      summary="Endpoint for api/faq",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *       )
     * )
     */
    public function faq(){}

    /**
     * @OA\Get(
     *      path="/api/favourite-artists",
     *      operationId="favourite.artists",
     *      tags={"User"},
     *      summary="Endpoint for api/favourite-artists",
     *      security={{"bearerAuth":{}}},
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *       )
     * )
     */
    public function favourite_artists(){}

    /**
     * @OA\Get(
     *      path="/api/feature",
     *      operationId="feature",
     *      tags={"Auth"},
     *      summary="Endpoint for api/feature",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *       )
     * )
     */
    public function feature(){}

    /**
     * @OA\Post(
     *      path="/api/forgot/password",
     *      operationId="forgot.password",
     *      tags={"Auth"},
     *      summary="Endpoint for api/forgot/password",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *       )
     * )
     */
    public function forgot_password(){}

    /**
     * @OA\Post(
     *      path="/api/forgot/pin",
     *      operationId="forgot.pin",
     *      tags={"Auth"},
     *      summary="Endpoint for api/forgot/pin",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *       )
     * )
     */
    public function forgot_pin(){}

    /**
     * @OA\Get(
     *      path="/api/genres",
     *      operationId="genres",
     *      tags={"Master"},
     *      summary="Endpoint for api/genres",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *       )
     * )
     */
    public function genres(){}

    /**
     * @OA\Get(
     *      path="/api/liked-songs",
     *      operationId="liked.songs",
     *      tags={"User"},
     *      summary="Endpoint for api/liked-songs",
     *      security={{"bearerAuth":{}}},
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *       )
     * )
     */
    public function liked_songs(){}

    /**
     * @OA\Post(
     *      path="/api/login",
     *      operationId="login",
     *      tags={"Auth"},
     *      summary="Endpoint for api/login",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *       )
     * )
     */
    public function login(){}

    /**
     * @OA\Post(
     *      path="/api/login-email",
     *      operationId="login-email",
     *      tags={"Auth"},
     *      summary="Endpoint for api/login-email",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *       )
     * )
     */
    public function login_email(){}

    /**
     * @OA\Post(
     *      path="/api/login/verification",
     *      operationId="login.verification",
     *      tags={"Auth"},
     *      summary="Endpoint for api/login/verification",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *       )
     * )
     */
    public function login_verification(){}

    /**
     * @OA\Post(
     *      path="/api/logout",
     *      operationId="logout",
     *      tags={"Auth"},
     *      summary="Endpoint for api/logout",
     *      security={{"bearerAuth":{}}},
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *       )
     * )
     */
    public function logout(){}

    /**
     * @OA\Get(
     *      path="/api/made-for-you",
     *      operationId="made.for.you",
     *      tags={"User"},
     *      summary="Endpoint for api/made-for-you",
     *      security={{"bearerAuth":{}}},
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *       )
     * )
     */
    public function made_for_you(){}

    /**
     * @OA\Get(
     *      path="/api/my-current-subscription",
     *      operationId="my-current-subscription",
     *      tags={"Subscription"},
     *      summary="Endpoint for api/my-current-subscription",
     *      security={{"bearerAuth":{}}},
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *       )
     * )
     */
    public function my_current_subscription(){}

    /**
     * @OA\Get(
     *      path="/api/my-profile",
     *      operationId="my-profile",
     *      tags={"Auth"},
     *      summary="Endpoint for api/my-profile",
     *      security={{"bearerAuth":{}}},
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *       )
     * )
     */
    public function my_profile(){}

    /**
     * @OA\Get(
     *      path="/api/pages",
     *      operationId="pages",
     *      tags={"Cms"},
     *      summary="Endpoint for api/pages",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *       )
     * )
     */
    public function pages(){}

    /**
     * @OA\Post(
     *      path="/api/playlist/add-remove-song",
     *      operationId="playlist.add-remove-song",
     *      tags={"Song"},
     *      summary="Endpoint for api/playlist/add-remove-song",
     *      security={{"bearerAuth":{}}},
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *       )
     * )
     */
    public function playlist_add_remove_song(){}

    /**
     * @OA\Post(
     *      path="/api/playlist/bulk-add-remove-song",
     *      operationId="playlist.bulk-add-remove-song",
     *      tags={"Song"},
     *      summary="Endpoint for api/playlist/bulk-add-remove-song",
     *      security={{"bearerAuth":{}}},
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *       )
     * )
     */
    public function playlist_bulk_add_remove_song(){}

    /**
     * @OA\Post(
     *      path="/api/playlist/create-or-update",
     *      operationId="playlist.create-or-update",
     *      tags={"Song"},
     *      summary="Endpoint for api/playlist/create-or-update",
     *      security={{"bearerAuth":{}}},
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *       )
     * )
     */
    public function playlist_create_or_update(){}

    /**
     * @OA\Get(
     *      path="/api/playlist/delete/{playlistId}",
     *      operationId="playlist.delete",
     *      tags={"Song"},
     *      summary="Endpoint for api/playlist/delete/{playlistId}",
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *          name="playlistId",
     *          in="path",
     *          required=true,
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *       )
     * )
     */
    public function playlist_delete(){}

    /**
     * @OA\Get(
     *      path="/api/playlist/details/{playlistId}",
     *      operationId="playlist.details",
     *      tags={"Song"},
     *      summary="Endpoint for api/playlist/details/{playlistId}",
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *          name="playlistId",
     *          in="path",
     *          required=true,
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *       )
     * )
     */
    public function playlist_details(){}

    /**
     * @OA\Get(
     *      path="/api/playlist/my-playlists",
     *      operationId="playlist.my-playlists",
     *      tags={"Song"},
     *      summary="Endpoint for api/playlist/my-playlists",
     *      security={{"bearerAuth":{}}},
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *       )
     * )
     */
    public function playlist_my_playlists(){}

    /**
     * @OA\Get(
     *      path="/api/radio/artist/{artistId}",
     *      operationId="radio.artist",
     *      tags={"Song"},
     *      summary="Endpoint for api/radio/artist/{artistId}",
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *          name="artistId",
     *          in="path",
     *          required=true,
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *       )
     * )
     */
    public function radio_artist(){}

    /**
     * @OA\Post(
     *      path="/api/raise-help",
     *      operationId="raise.help",
     *      tags={"HelpAndSupport"},
     *      summary="Endpoint for api/raise-help",
     *      security={{"bearerAuth":{}}},
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *       )
     * )
     */
    public function raise_help(){}

    /**
     * @OA\Get(
     *      path="/api/recently-played",
     *      operationId="recently.played",
     *      tags={"User"},
     *      summary="Endpoint for api/recently-played",
     *      security={{"bearerAuth":{}}},
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *       )
     * )
     */
    public function recently_played(){}

    /**
     * @OA\Get(
     *      path="/api/recommend-songs",
     *      operationId="recommend.songs",
     *      tags={"Song"},
     *      summary="Endpoint for api/recommend-songs",
     *      security={{"bearerAuth":{}}},
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *       )
     * )
     */
    public function recommend_songs(){}

    /**
     * @OA\Post(
     *      path="/api/reset/password",
     *      operationId="reset.password",
     *      tags={"Auth"},
     *      summary="Endpoint for api/reset/password",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *       )
     * )
     */
    public function reset_password(){}

    /**
     * @OA\Post(
     *      path="/api/search",
     *      operationId="search.songs",
     *      tags={"Song"},
     *      summary="Endpoint for api/search",
     *      security={{"bearerAuth":{}}},
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *       )
     * )
     */
    public function search_songs(){}

    /**
     * @OA\Get(
     *      path="/api/service/frequency/list",
     *      operationId="service.frequency.list",
     *      tags={"Auth"},
     *      summary="Endpoint for api/service/frequency/list",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *       )
     * )
     */
    public function service_frequency_list(){}

    /**
     * @OA\Get(
     *      path="/api/setting",
     *      operationId="setting",
     *      tags={"Auth"},
     *      summary="Endpoint for api/setting",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *       )
     * )
     */
    public function setting(){}

    /**
     * @OA\Post(
     *      path="/api/signup",
     *      operationId="signup",
     *      tags={"Auth"},
     *      summary="Endpoint for api/signup",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *       )
     * )
     */
    public function signup(){}

    /**
     * @OA\Post(
     *      path="/api/song/add-play-history/{id}",
     *      operationId="song.add-play-history",
     *      tags={"Master"},
     *      summary="Endpoint for api/song/add-play-history/{id}",
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *       )
     * )
     */
    public function song_add_play_history(){}

    /**
     * @OA\Post(
     *      path="/api/song/increase-play-count/{id}",
     *      operationId="song.increase-play-count",
     *      tags={"Master"},
     *      summary="Endpoint for api/song/increase-play-count/{id}",
     *      @OA\Parameter(
     *          name="id",
     *          in="path",
     *          required=true,
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *       )
     * )
     */
    public function song_increase_play_count(){}

    /**
     * @OA\Get(
     *      path="/api/songs-by-album/{albumId}",
     *      operationId="songs.by.album",
     *      tags={"Master"},
     *      summary="Endpoint for api/songs-by-album/{albumId}",
     *      @OA\Parameter(
     *          name="albumId",
     *          in="path",
     *          required=true,
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *       )
     * )
     */
    public function songs_by_album(){}

    /**
     * @OA\Post(
     *      path="/api/stripe/webhook",
     *      operationId="api.stripe.webhook",
     *      tags={"StripeWebhook"},
     *      summary="Endpoint for api/stripe/webhook",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *       )
     * )
     */
    public function api_stripe_webhook(){}

    /**
     * @OA\Get(
     *      path="/api/subscriptions",
     *      operationId="subscriptions",
     *      tags={"Subscription"},
     *      summary="Endpoint for api/subscriptions",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *       )
     * )
     */
    public function subscriptions(){}

    /**
     * @OA\Get(
     *      path="/api/support-articles",
     *      operationId="support.articles",
     *      tags={"HelpAndSupport"},
     *      summary="Endpoint for api/support-articles",
     *      security={{"bearerAuth":{}}},
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *       )
     * )
     */
    public function support_articles(){}

    /**
     * @OA\Post(
     *      path="/api/todo/add",
     *      operationId="todo.add",
     *      tags={"Auth"},
     *      summary="Endpoint for api/todo/add",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *       )
     * )
     */
    public function todo_add(){}

    /**
     * @OA\Post(
     *      path="/api/todo/delete",
     *      operationId="todo.delete",
     *      tags={"Auth"},
     *      summary="Endpoint for api/todo/delete",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *       )
     * )
     */
    public function todo_delete(){}

    /**
     * @OA\Get(
     *      path="/api/todo/list",
     *      operationId="todo.list",
     *      tags={"Auth"},
     *      summary="Endpoint for api/todo/list",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *       )
     * )
     */
    public function todo_list(){}

    /**
     * @OA\Get(
     *      path="/api/toggle-artist-follow/{artistId}",
     *      operationId="toggle.artist.follow",
     *      tags={"User"},
     *      summary="Endpoint for api/toggle-artist-follow/{artistId}",
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *          name="artistId",
     *          in="path",
     *          required=true,
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *       )
     * )
     */
    public function toggle_artist_follow(){}

    /**
     * @OA\Post(
     *      path="/api/toggle-artist-preference",
     *      operationId="toggle.artist.preference",
     *      tags={"User"},
     *      summary="Endpoint for api/toggle-artist-preference",
     *      security={{"bearerAuth":{}}},
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *       )
     * )
     */
    public function toggle_artist_preference(){}

    /**
     * @OA\Get(
     *      path="/api/toggle-song-like/{songId}",
     *      operationId="toggle.song.like",
     *      tags={"User"},
     *      summary="Endpoint for api/toggle-song-like/{songId}",
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *          name="songId",
     *          in="path",
     *          required=true,
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *       )
     * )
     */
    public function toggle_song_like(){}

    /**
     * @OA\Get(
     *      path="/api/trending-search-items",
     *      operationId="trending.search.items",
     *      tags={"Song"},
     *      summary="Endpoint for api/trending-search-items",
     *      security={{"bearerAuth":{}}},
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *       )
     * )
     */
    public function trending_search_items(){}

    /**
     * @OA\Post(
     *      path="/api/update-profile",
     *      operationId="update-profile",
     *      tags={"Auth"},
     *      summary="Endpoint for api/update-profile",
     *      security={{"bearerAuth":{}}},
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *       )
     * )
     */
    public function update_profile(){}

    /**
     * @OA\Post(
     *      path="/api/user/subscription/purchase",
     *      operationId="user.subscription.purchase",
     *      tags={"Subscription"},
     *      summary="Endpoint for api/user/subscription/purchase",
     *      security={{"bearerAuth":{}}},
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *       )
     * )
     */
    public function user_subscription_purchase(){}

    /**
     * @OA\Post(
     *      path="/api/verify/pin",
     *      operationId="verify.pin",
     *      tags={"Auth"},
     *      summary="Endpoint for api/verify/pin",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *       )
     * )
     */
    public function verify_pin(){}

}
