<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\AuthResource;
use App\Http\Controllers\BaseController;
use App\Http\Resources\Api\ArtistResource;
use App\Http\Resources\Api\PaginateArtistCollection;
use Illuminate\Support\Facades\Validator;

class ArtistController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/api/artists",
     *     summary="Get all artists",
     *     tags={"Artist"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="keyword",
     *         in="query",
     *         description="Search keyword for artist name",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of items per page (default: 15)",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number for pagination",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Artists fetched successfully")
     * )
     */
    public function artists(Request $request)
    {
        try {
            $perPage = $request->per_page ?? 15;
            $artists = User::whereHas('roles', function ($q) {
                $q->where('slug', 'artist');
            })
            ->when($request->filled('keyword'), function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->keyword . '%');
            })
            ->whereNull('added_by')->paginate($perPage);
            return $this->responseJson(true, 200, 'Artists fetched successfully', new PaginateArtistCollection($artists));
        } catch (\Throwable $th) {
            logger($th->getMessage() . '--' . $th->getLine() . '--' . $th->getFile());
            return $this->responseJson(false, 500, 'Something went wrong', []);
        }
    }
    /**
     * @OA\Get(
     *     path="/api/artist/details/{id}",
     *     summary="Get artist details",
     *     tags={"Artist"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(response=200, description="Artist details fetched")
     * )
     */
    public function artistDetails($id)
    {
        $validator = Validator::make(['id' => $id], [
            'id' => 'required|exists:users,id',
        ]);
        if ($validator->fails()) {
            return $this->responseJson(false, 422, $validator->errors()->first(), []);
        }
        $artist = User::with(['songs.album', 'songs.genre', 'albums'])->find($id);
        return $this->responseJson(true, 200, 'Artist details fetched successfully', new AuthResource($artist));
    }
}
