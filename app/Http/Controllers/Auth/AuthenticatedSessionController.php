<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        $user = $request->user(); // Récupère l'utilisateur connecté

        // Redirection en fonction du rôle
        return match (true) {
            $user->hasRole('admin') => redirect()->route('dashboard.admin'),
            $user->hasRole('directeur') => redirect()->route('dashboard.directeur'),
            $user->hasRole('responsable') => redirect()->route('dashboard.responsable'),
            $user->hasRole('collaborateur') => redirect()->route('dashboard.collaborateur'),
            default => abort(403, 'Aucun rôle valide associé à votre compte.'), // Fallback (ex: page d'accueil neutre ou erreur)
        };
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}