<?php

namespace App\Http\Middleware;

use App\Support\RoleRedirect;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if (! $user->role) {
            return redirect()->route('login')->with('status', 'Your account role is not configured.');
        }

        if (! in_array($user->role, $roles, true)) {
            return redirect()
                ->route(RoleRedirect::routeFor($user))
                ->with('status', 'You are not allowed to access that page.');
        }

        return $next($request);
    }
}
