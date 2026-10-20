<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireStaff
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->guest(route('login'));
        }

        if ($user->isPortalUser() && ! $user->isStaffUser()) {
            return redirect()->route($user->homeRouteName());
        }

        abort_unless($user->isStaffUser() || $user->is_system, 403);

        return $next($request);
    }
}
