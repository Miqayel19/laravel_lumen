<?php

namespace App\Http\Middleware;

use App\User;
use App\UserRoleTeam;
use Closure;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $id = $request->id;
        $token = $request->headers->get('token');
        $user = User::where('token', $token)->first();
        $team_owner = UserRoleTeam::where([['user_id', $user->id], ['team_id', $id]])->first();
        if ($token && $user) {
            if (!$team_owner) {
                return response()->json(['status' => 'failed', 'message' => 'You dont have an access'], 200);
            }
        } else {
            return response()->json(['status' => 'success', 'Invalid token or Invalid user']);
        }

        return $next($request);
    }
}
