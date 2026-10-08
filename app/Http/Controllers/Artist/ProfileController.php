<?php

namespace App\Http\Controllers\Artist;

use App\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ArtistProfile;
use App\Models\SocialLink;
use App\Models\Genre;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ProfileController extends BaseController
{
    public function index()
    {
        $user = auth()->user();
        $profile = ArtistProfile::firstOrCreate(['user_id' => $user->id]);
        $socials = SocialLink::firstOrCreate(['user_id' => $user->id]);
        $genres = Genre::where('is_active', 1)->get();

        return view('artist.profile.index', compact('user', 'profile', 'socials', 'genres'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'stage_name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)->whereNull('deleted_at')],
            'mobile' => ['required', 'numeric', Rule::unique('users', 'mobile_number')->ignore($user->id)->whereNull('deleted_at')],
            'country' => 'required|string|max:100',
            'bio' => 'required|string|max:5000',
            'primary_genre_id' => 'required|exists:genres,id',
            'sub_genre_id' => 'nullable|exists:genres,id',
            'label' => 'nullable|string|max:255',
            'years_of_active' => 'required|integer|min:0',
            'website' => 'nullable|url|max:255',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'cover_banner' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
            'instagram_url' => 'nullable|url|max:255',
            'youtube_url' => 'nullable|url|max:255',
            'tiktok_url' => 'nullable|url|max:255',
            'facebook_url' => 'nullable|url|max:255',
            'twitter_url' => 'nullable|url|max:255',
            'spotify_url' => 'nullable|url|max:255',
            'apple_music_url' => 'nullable|url|max:255',
        ]);

        DB::beginTransaction();
        try {
            $user->name = $request->name;
            $user->email = $request->email;
            $user->mobile_number = $request->mobile;

            if ($request->hasFile('profile_image')) {
                $file = $request->file('profile_image');
                $filename = time() . '_profile_' . rand(100, 999) . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('storage/profile'), $filename);
                $user->profile_image = $filename;
            }

            $user->save();

            $profileData = [
                'display_name' => $request->stage_name,
                'country' => $request->country,
                'bio' => $request->bio,
                'primary_genre_id' => $request->primary_genre_id,
                'sub_genre_id' => $request->sub_genre_id,
                'label' => $request->label,
                'years_of_active' => $request->years_of_active,
                'website' => $request->website,
            ];

            if (isset($filename)) {
                $profileData['profile_image'] = $filename;
            }

            if ($request->hasFile('cover_banner')) {
                $file = $request->file('cover_banner');
                $filename_cover = time() . '_banner_' . rand(100, 999) . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('storage/banner'), $filename_cover);
                $profileData['cover_banner'] = $filename_cover;
            }

            ArtistProfile::updateOrCreate(['user_id' => $user->id], $profileData);

            SocialLink::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'instagram_url' => $request->instagram_url,
                    'youtube_url' => $request->youtube_url,
                    'tiktok_url' => $request->tiktok_url,
                    'facebook_url' => $request->facebook_url,
                    'twitter_url' => $request->twitter_url,
                    'spotify_url' => $request->spotify_url,
                    'apple_music_url' => $request->apple_music_url,
                ]
            );

            DB::commit();

            return redirect()->back()->with('success', 'Profile updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }
}
