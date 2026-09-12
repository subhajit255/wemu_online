<?php

namespace App\Http\Controllers\Admin;

use App\Models\Song;
use App\Models\Album;
use App\Models\Genre;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Traits\CommonFunction;
use App\Http\Controllers\BaseController;

class GenreController extends BaseController
{
    use CommonFunction;

    public function index(Request $request)
    {
        $query = Genre::query();
        
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }
        
        if ($request->filled('status')) {
            $query->where('is_active', $request->status);
        }

        $details = $query->latest()->get();
        $genresOption = $this->categoryDropdownOptions(Genre::all());
        
        return view('admin.genre.index', compact('details', 'genresOption'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255'
        ]);

        $message = "Genre successfully added";
        
        if (!empty($request->id)) {
            $genre = Genre::find($request->id);
            if (!$genre) {
                return $this->responseJson(false, 404, 'Genre not found', []);
            }
            $message = "Genre successfully updated";
        } else {
            $genre = new Genre();
        }

        $genre->title = $request->title;
        $genre->slug = Str::slug($request->title);
        $genre->parent_id = $request->parent_id;
        $genre->save();

        $response = [
            'redirect' => route('admin.genre.list')
        ];
        
        if ($request->ajax()) {
            return $this->responseJson(true, 200, $message, $response);
        }
        
        return response()->json(['status' => true, 'message' => $message, 'url' => route('admin.genre.list')]);
    }
    
    public function delete(Request $request)
    {
        $id = $request->id ?? $request->uuid; // fallback to uuid if frontend js passes it as uuid parameter
        $genre = Genre::find($id);
        
        if (!$genre) {
            return $this->responseJson(false, 404, "Genre not found", []);
        }

        // Check if there are any songs or albums attached
        $songsCount = Song::where('genre_id', $id)->count();
        $albumsCount = Album::where('genre_id', $id)->count();

        if ($songsCount > 0 || $albumsCount > 0) {
            return $this->responseJson(false, 400, "Cannot delete genre. It is associated with songs or albums.", []);
        }

        if ($genre->delete()) {
            return $this->responseJson(true, 200, "Genre successfully deleted", []);
        }
        return $this->responseJson(false, 500, "Something went wrong", []);
    }
}
