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
            \Log::info('=== INÍCIO DO PROCESSO DE LOGIN ===', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            // Verifica estado antes da autenticação
            \Log::info('Estado pré-autenticação', [
                'has_session' => $request->hasSession(),
                'session_id' => session()->getId(),
                'auth_check' => Auth::check()
            ]);

            // Tenta autenticar
            $request->authenticate();

            // Verifica estado após autenticação
            \Log::info('Estado pós-autenticação', [
                'auth_check' => Auth::check(),
                'user_id' => Auth::id(),
                'session_id' => session()->getId()
            ]);

            // Regenera a sessão
            $request->session()->regenerate();

            // Armazena dados importantes na sessão
            session([
                'user_id' => Auth::id(),
                'auth_check' => true
            ]);

            // Força o salvamento
            session()->save();

            // Log final
            \Log::info('Estado final antes do redirecionamento', [
                'auth_check' => Auth::check(),
                'session_data' => session()->all(),
                'cookies' => $request->cookies->all()
            ]);

            \Log::info('=== FIM DO PROCESSO DE LOGIN ===');

            return redirect()
                ->intended(route('home'))
                ->withHeaders([
                    'Cache-Control' => 'no-cache, no-store, must-revalidate',
                    'Pragma' => 'no-cache',
                    'Expires' => '0'
                ]);

        } catch (\Exception $e) {
            \Log::error('Erro durante o login', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
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
