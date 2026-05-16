<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LogoutController extends Controller
{
    public function __construct(private readonly AuthService $authService) {}

    public function destroy(Request $request): RedirectResponse
    {
        $this->authService->logout();

        return redirect()->route('login')
                         ->with('success', 'Vous avez été déconnecté avec succès.');
    }
}
