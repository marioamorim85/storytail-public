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
            \Log::info('Configurações de sessão', [
                'session_driver' => config('session.driver'),
                'session_domain' => config('session.domain'),
                'session_secure' => config('session.secure'),
                'session_same_site' => config('session.same_site')
            ]);

            // Autentica o usuário antes de qualquer manipulação de sessão
            $request->authenticate();
            $user = Auth::user();

            \Log::info('Após autenticação', [
                'user_id' => $user->id,
                'auth_check' => Auth::check()
            ]);

            // Regenera a sessão e mantém os dados
            $request->session()->regenerate(true);

            // Armazena dados na sessão
            $sessionData = [
                'auth.id' => $user->id,
                'auth.email' => $user->email,
                'auth.name' => $user->first_name . ' ' . $user->last_name,
                'auth.logged_in' => true,
                'auth.timestamp' => now()->toISOString()
            ];

            foreach ($sessionData as $key => $value) {
                session([$key => $value]);
            }

            // Força o salvamento e verifica
            session()->save();

            \Log::info('Estado final da sessão', [
                'session_id' => session()->getId(),
                'is_authenticated' => Auth::check(),
                'session_data' => session()->all(),
                'cookie_data' => $request->cookie()
            ]);

            \Log::info('=== FIM DO PROCESSO DE LOGIN ===');

            // Cria o cookie de autenticação
            $cookie = cookie('auth_remember', true, 120, null, config('session.domain'), true, true, false, 'lax');

            return redirect()
                ->intended(route('home'))
                ->withHeaders([
                    'Cache-Control' => 'no-cache, no-store, must-revalidate',
                    'Pragma' => 'no-cache',
                    'Expires' => '0'
                ])
                ->withCookie($cookie)
                ->with('success', 'Welcome back ' . $user->first_name . ' ' . $user->last_name . '!');

        } catch (\Exception $e) {
            \Log::error('Erro durante login', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
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
