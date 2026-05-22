<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\User;
use App\Traits\UploadAble;
use Illuminate\Http\Request;
use App\Traits\CommonFunction;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\BaseController;

class ArtistController extends BaseController
{
    use CommonFunction;
    use UploadAble;

    public function index(Request $request)
    {
        // Artists are user_type = 3
        $query = User::where('user_type', 3)->withCount(['songs', 'albums'])->latest();
        
        if (($request->type == 1 || $request->type == 2) && $request->type != null) {
            $query->where('is_active', $request->type == 2 ? 0 : 1);
        }
        
        if ($search = $request->search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('mobile_number', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        
        $details = $query->paginate(10);
        return view('admin.artist.index', compact('details'));
    }

    public function add(Request $request)
    {
        $user = array();
        if ($request->post()) {
            $id = $request->id ?? NULL;
            if (!empty($id)) {
                $request->validate([
                    'name' => 'required|string',
                    'email' => 'required|email|unique:users,email,' . $id,
                    'mobile_number' => 'required|numeric|unique:users,mobile_number,' . $id,
                    'file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10000',
                ]);
                $message = "Artist Updated Successfully";
            } else {
                $request->validate([
                    'name' => 'required|string',
                    'email' => 'required|email|unique:users,email',
                    'mobile_number' => 'required|numeric|digits:10|unique:users,mobile_number',
                    'file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10000',
                ]);
                $message = "Artist Created Successfully";
            }

            DB::beginTransaction();
            try {
                $password = '12345678';
                $postData = [
                    "name" => $request->name,
                    'username' => $request->username,
                    "mobile_number" => $request->mobile_number,
                    "email" => $request->email,
                    'user_type' => 3, // Artist is user_type = 3
                    'is_approve' => 1,
                    'is_verified' => 1,
                    "password" => bcrypt($password)
                ];
                if (!empty($request->file)) {
                    $image = $request->file;
                    $fileName = uniqid() . '.' . $image->getClientOriginalExtension();
                    $isFileUploaded = $this->uploadOne($image, config('constants.SITE_PROFILE_IMAGE_UPLOAD_PATH'), $fileName, 'public');
                    if ($isFileUploaded) {
                        $postData['profile_image'] = $fileName;
                    }
                }
                $user = User::updateOrCreate(['id' => $id], $postData);
                DB::Commit();
            } catch (\Throwable $th) {
                DB::rollback();
                $status = false;
                $code = 500;
                $response = ['Message' => $th->getMessage(), 'File Path' => $th->getFile(), 'Line Number' => $th->getLine()];
                $message = config('constants.CATCH_ERROR_MSG');
                return $this->responseJson($status, $code, $message, $response);
            }

            $data = ['status' => true, 'message' => $message, 'data' => $user, 'url' => route('admin.artist.list')];
            return response($data);
        }

        $details = array();
        if (!empty($request->uuid)) {
            $uuid = uuidtoid($request->uuid, 'users');
            $details = User::find($uuid);
        }
        return view('admin.artist.add', compact('details'));
    }

    public function view(Request $request)
    {
        $detail = User::where('uuid', $request->uuid)
            ->with(['songs', 'albums', 'profile.primaryGenre', 'profile.subGenre', 'preference', 'verification', 'socialLink'])
            ->firstOrFail();
        return view('admin.artist.view', compact('detail'));
    }

    public function approve(Request $request, $uuid)
    {
        DB::beginTransaction();
        try {
            $user = User::where('uuid', $uuid)->firstOrFail();
            
            // Update User status
            $user->is_approve = 1;
            $user->is_verified = 1;
            $user->save();
            
            // Update ArtistProfile status
            if ($user->profile) {
                $user->profile->update([
                    'is_verified' => 1
                ]);
            }
            
            // Update ArtistVerification status
            if ($user->verification) {
                $user->verification->update([
                    'verification_status' => 1,
                    'verified_at' => now(),
                    'rejection_reason' => null
                ]);
            } else {
                $user->verification()->create([
                    'verification_status' => 1,
                    'verified_at' => now(),
                ]);
            }
            
            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Artist verified and approved successfully!'
            ]);
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json([
                'status' => false,
                'message' => 'Error: ' . $th->getMessage()
            ], 500);
        }
    }

    public function reject(Request $request, $uuid)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:1000'
        ]);

        DB::beginTransaction();
        try {
            $user = User::where('uuid', $uuid)->firstOrFail();
            
            // Update User status
            $user->is_approve = 0;
            $user->is_verified = 0;
            $user->save();
            
            // Update ArtistProfile status
            if ($user->profile) {
                $user->profile->update([
                    'is_verified' => 0
                ]);
            }
            
            // Update ArtistVerification status
            if ($user->verification) {
                $user->verification->update([
                    'verification_status' => 2,
                    'rejection_reason' => $request->rejection_reason
                ]);
            } else {
                $user->verification()->create([
                    'verification_status' => 2,
                    'rejection_reason' => $request->rejection_reason
                ]);
            }
            
            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Artist verification rejected successfully.'
            ]);
        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json([
                'status' => false,
                'message' => 'Error: ' . $th->getMessage()
            ], 500);
        }
    }
}
