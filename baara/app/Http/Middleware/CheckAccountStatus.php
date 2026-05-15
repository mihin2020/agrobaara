<?php

namespace App\Http\Middleware;

use App\Enums\UserStatus;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckAccountStatus
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return $next($request);
        }

        if ($user->status === UserStatus::Inactive) {
            Auth::logout();
            return redirect()->route('login')
                             ->withErrors(['email' => 'Votre compte a été désactivé.']);
        }

        if ($user->isLocked()) {
            Auth::logout();
            return redirect()->route('login')
                             ->withErrors(['email' => 'Votre compte est temporairement verrouillé.']);
        }

        // Forcer changement de mot de passe à la première connexion
        if ($user->needsPasswordChange()
            && !$request->routeIs('password.change', 'password.change.update')
        ) {
            return redirect()->route('password.change');
        }

        return $next($request);
    }
}
