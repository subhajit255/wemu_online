<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\BaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Album;
use App\Models\Language;
use App\Models\Genre;
use App\Models\User;
use App\Traits\UploadAble;

class AlbumController extends BaseController
{
    use UploadAble;

    public function index(Request $request)
    {
        $albums = Album::latest()->paginate(8);
        return view('admin.album.index', compact('albums'));
    }

    public function StoreOrUpdate(Request $request)
    {
        if ($request->post()) {
            $id = $request->id ?? NULL;
            $rules = [
                'title' => 'required|string',
                'user_id' => 'required|exists:users,id',
                'genre_id' => 'required|exists:genres,id',
                'status' => 'required|in:0,1',
                'release_date' => 'nullable|date',
            ];

            if (!empty($id)) {
                $rules['file'] = 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10000';
                $message = "Album Updated Successfully";
            } else {
                $rules['file'] = 'required|image|mimes:jpeg,png,jpg,gif,svg|max:10000';
                $message = "Album Created Successfully";
            }

            $request->validate($rules);

            DB::beginTransaction();
            try {
                $isLanguage = Language::where('slug', 'english')->first();
                $postData = [
                    'title' => $request->title,
                    'description' => $request->description ?? null,
                    'slug' => str::slug($request->title),
                    'genre_id' => $request->genre_id,
                    'language_id' => $isLanguage->id ?? 1,
                    'user_id' => $request->user_id,
                    'status' => $request->status ?? 0,
                    'release_date' => $request->release_date ?? Date('Y-m-d'),
                ];
                if (!empty($request->file)) {
                    $image = $request->file;
                    $fileName = uniqid() . '.' . $image->getClientOriginalExtension();
                    $isFileUploaded = $this->uploadOne($image, config('constants.SITE_ALBUM_UPLOAD_PATH'), $fileName, 'public');
                    if ($isFileUploaded) {
                        $postData['cover_image'] = $fileName;
                    }
                }
                $album = Album::updateOrCreate(['id' => $id], $postData);
                DB::Commit();
            } catch (\Throwable $th) {
                DB::rollback();
                $status = false;
                $code = 500;
                $response = ['Message' => $th->getMessage(), 'File Path' => $th->getFile(), 'Line Number' => $th->getLine()];
                $message = config('constants.CATCH_ERROR_MSG');
                return $this->responseJson($status, $code, $message, $response);
            }

            $data = ['status' => true, 'message' => $message, 'data' => $album, 'url' => route('admin.albums.index')];
            return response($data);
        }

        $details = [];
        if (!empty($request->id)) {
            $details = Album::find($request->id);
        }
        $genres = Genre::where('is_active', 1)->get();
        $artists = User::where('user_type', 3)->latest()->get();

        return view('admin.album.form', compact('details', 'genres', 'artists'));
    }

    public function show($id)
    {
        $albumDetails = Album::with('songs')->find($id);
        return view('admin.album.show', compact('albumDetails'));
    }
}
