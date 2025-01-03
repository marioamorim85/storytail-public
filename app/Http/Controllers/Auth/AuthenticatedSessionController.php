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
            \Log::info('=== INÍCIO DO PROCESSO DE LOGIN ===');

            // Verifica estado inicial
            \Log::info('Estado inicial', [
                'session_exists' => $request->hasSession(),
                'old_session_id' => session()->getId(),
                'driver' => config('session.driver'),
                'domain' => config('session.domain')
            ]);

            // Autentica o usuário
            $request->authenticate();

            // Recupera o usuário atual
            $user = Auth::user();

            // Regenera a sessão
            $request->session()->regenerate();

            // Armazena dados na sessão
            session([
                'auth.id' => $user->id,
                'auth.email' => $user->email,
                'auth.name' => $user->first_name . ' ' . $user->last_name,
                'auth.logged_in' => true,
                'auth.timestamp' => now()->toISOString()
            ]);

            // Força o salvamento da sessão
            session()->save();

            // Log de verificação final
            \Log::info('Estado final', [
                'new_session_id' => session()->getId(),
                'user_authenticated' => Auth::check(),
                'user_id' => Auth::id(),
                'session_data' => session()->all()
            ]);

            \Log::info('=== FIM DO PROCESSO DE LOGIN ===');

            return redirect()
                ->intended(route('home'))
                ->withHeaders([
                    'Cache-Control' => 'no-cache, no-store, must-revalidate',
                    'Pragma' => 'no-cache',
                    'Expires' => '0'
                ])
                ->with('success', 'Welcome back ' . $user->first_name . ' ' . $user->last_name . '!');

        } catch (\Exception $e) {
            \Log::error('Erro no login', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'session_data' => session()->all()
            ]);

            return back()
                ->withErrors(['email' => 'Authentication failed. Please try again.'])
                ->withInput($request->only('email'));
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
