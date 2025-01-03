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
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        try {
            \Log::info('=== INÍCIO DO LOGIN ===');

            // Verifica estado inicial
            \Log::info('Estado inicial', [
                'auth_check' => Auth::check(),
                'session_id' => session()->getId()
            ]);

            $request->authenticate();

            \Log::info('Após authenticate()', [
                'auth_check' => Auth::check(),
                'user_id' => Auth::id()
            ]);

            $request->session()->regenerate();

            \Log::info('Após regenerate()', [
                'auth_check' => Auth::check(),
                'session_id' => session()->getId()
            ]);

            // Força persistência da sessão
            $user = Auth::user();
            session(['user_id' => $user->id]);
            session()->save();

            \Log::info('Estado final', [
                'auth_check' => Auth::check(),
                'session_data' => session()->all()
            ]);

            return redirect()
                ->intended(route('home', absolute: false))
                ->with('success', 'Welcome back ' . $user->first_name . ' ' . $user->last_name . '!')
                ->withHeaders([
                    'Cache-Control' => 'no-cache, no-store, must-revalidate',
                    'Pragma' => 'no-cache',
                    'Expires' => '0'
                ]);

        } catch (\Exception $e) {
            \Log::error('Erro no login', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->withErrors(['email' => 'Authentication failed.']);
        }
    }

    public function destroy(Request $request): RedirectResponse
    {
        try {
            $userName = Auth::user()->first_name . ' ' . Auth::user()->last_name;

            \Log::info('Logout attempt', [
                'user_id' => Auth::id(),
                'session_id' => session()->getId()
            ]);

            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            \Log::info('Logout successful', [
                'previous_session_id' => session()->getId()
            ]);

            return redirect('/')
                ->with('info', 'Goodbye ' . $userName . '! You have successfully logged out.');
        } catch (\Exception $e) {
            \Log::error('Logout error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect('/');
        }
    }
}
