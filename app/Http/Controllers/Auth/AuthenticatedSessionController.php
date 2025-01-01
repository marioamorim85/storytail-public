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
        try {
            // Regista a tentativa de login
            \Log::info('Login attempt', [
                'email' => $request->input('email'),
                'ip' => $request->ip(),
                'session_id' => $request->session()->getId(),
            ]);

            // Regenera o token CSRF antes da autenticação
            $request->session()->regenerateToken();

            // Tenta autenticar
            $request->authenticate();

            // Regenera a sessão após autenticação bem-sucedida
            $request->session()->regenerate();

            // Redireciona com mensagem de boas-vindas
            return redirect()->intended(route('home', absolute: false))
                ->with('success', 'Welcome back ' . Auth::user()->first_name . ' ' . Auth::user()->last_name . '! You have successfully logged in.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Trata erros de validação (credenciais inválidas)
            \Log::warning('Login validation failed', [
                'email' => $request->input('email'),
                'errors' => $e->errors()
            ]);

            return back()
                ->withErrors($e->errors())
                ->withInput($request->only('email'));

        } catch (\Exception $e) {
            // Regista qualquer outro erro inesperado
            \Log::error('Login error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()
                ->withErrors([
                    'email' => 'An unexpected error occurred. Please try again.',
                ])
                ->withInput($request->only('email'));
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $userName = Auth::user()->first_name . ' ' . Auth::user()->last_name;

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/')
            ->with('info', 'Goodbye ' . $userName . '! You have successfully logged out.');
    }
}
