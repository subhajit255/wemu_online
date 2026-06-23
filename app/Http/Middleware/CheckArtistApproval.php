<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckArtistApproval
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        
        if ($user && $user->user_type == 3 && $user->is_approve == 0) {
            // Check if they are a team member (added_by is set)
            // If they are a team member, they might be exempt from approval,
            // or the approval of the main artist should be checked?
            // Actually, if the main artist is unapproved, team members should be blocked too.
            $mainArtistId = $user->added_by ?: $user->id;
            $mainArtist = \App\Models\User::find($mainArtistId);
            
            if ($mainArtist && $mainArtist->is_approve == 0) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'status' => false, 
                        'message' => 'Account is pending admin approval. Access restricted.'
                    ], 403);
                }
                
                return redirect()->route('artist.dashboard')->with('error', 'Your account is pending admin approval.');
            }
        }
        
        return $next($request);
    }
}
