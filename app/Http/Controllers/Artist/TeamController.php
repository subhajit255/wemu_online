<?php

namespace App\Http\Controllers\Artist;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\TeamMemberInvitationMail;

class TeamController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (auth()->check() && auth()->user()->added_by) {
                return redirect()->route('artist.dashboard')->with('error', 'Unauthorized access.');
            }
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $query = User::where('added_by', auth()->user()->id);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('mobile_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status);
        }

        $users = $query->latest()->paginate(1);
        return view('artist.team.index', compact('users'));
    }

    public function storeOrUpdate(Request $request)
    {
        if ($request->post()) {
            $id = $request->id ?? null;
            $role = Role::where('slug', 'artist')->first();
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:users,email' . ($id ? ',' . $id : ''),
                'phone' => 'nullable|string|max:20',
            ]);

            $userData = [
                'name' => $request->name,
                'email' => $request->email,
                'user_type' => $role->id,
                'mobile_number' => $request->phone,
                'added_by' => auth()->user()->id,
                'permissions' => json_encode($request->permissions ?? []),
            ];

            if (!$id) {
                $userData['password'] = Hash::make('secret');
            }

            $user = User::updateOrCreate(
                ['id' => $id],
                $userData
            );
            if ($role) {
                $user->roles()->syncWithoutDetaching([$role->id]);
            }

            if (!$id) {
                Mail::to($user->email)->send(new TeamMemberInvitationMail($user, 'secret'));
            }

            return response()->json(['status' => 200, 'message' => 'Team member saved successfully']);
        }
    }

    public function updatePermissions(Request $request, $id)
    {
        $user = User::where('id', $id)->where('added_by', auth()->user()->id)->first();
        if (!$user) {
            return response()->json(['status' => 404, 'message' => 'Team member not found']);
        }

        $user->update([
            'permissions' => json_encode($request->permissions ?? [])
        ]);

        return response()->json(['status' => 200, 'message' => 'Permissions updated successfully']);
    }

    public function destroy($id)
    {
        $user = User::where('id', $id)->where('added_by', auth()->user()->id)->first();

        if (!$user) {
            return response()->json(['status' => 404, 'message' => 'Team member not found']);
        }

        if ($user->songs()->exists() || $user->albums()->exists()) {
            return response()->json(['status' => 400, 'message' => 'Cannot delete team member as they are associated with songs or albums']);
        }

        $user->delete();

        return response()->json(['status' => 200, 'message' => 'Team member deleted successfully']);
    }
}
