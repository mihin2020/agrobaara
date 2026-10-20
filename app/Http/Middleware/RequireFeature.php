<?php

namespace App\Http\Middleware;

use App\Support\Feature;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireFeature
{
    public function handle(Request $request, Closure $next, string $flag): Response
    {
        if (! Feature::enabled($flag)) {
            abort(404);
        }

        return $next($request);
    }
}
