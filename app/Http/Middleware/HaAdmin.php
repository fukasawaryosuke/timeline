<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;

class HaAdmin
{
    /**
     * 管理者チェックmiddleware
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $humanId = request()->getHumanObject()->human_id;
        $user = User::where('human_id', $humanId)->first();

        if (is_null($user)) {
            abort(403);
        }

        if (!$user->SystemRole->isAdmin()) {
            abort(403);
        }

        return $next($request);
    }
}
