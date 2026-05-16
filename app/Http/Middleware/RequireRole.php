<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        if (!$user->hasAnyRole($roles)) {
            abort(403, 'Accès refusé. Vous ne disposez pas des droits nécessaires.');
        }

        return $next($request);
    }
}
