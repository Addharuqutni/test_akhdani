<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (!$user || !$user->role || !in_array($user->role->code, $roles, true)) {
            abort(403, 'Akses ditolak.');
        }

        return $next($request);
    }
}
