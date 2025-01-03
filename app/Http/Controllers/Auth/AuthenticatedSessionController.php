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

    public function store(LoginRequest $request): RedirectResponse
    {
        try {
            // Log antes da autenticação com informações detalhadas
            \Log::info('Login attempt', [
                'email' => $request->input('email'),
                'ip' => $request->ip(),
                'session_id' => session()->getId(),
                'session_driver' => config('session.driver'),
                'session_domain' => config('session.domain'),
                'cookies' => request()->cookies()->all(),
                'headers' => request()->headers->all()
            ]);

            // Regenera o token CSRF antes da autenticação
            $request->session()->regenerateToken();

            // Tenta autenticar
            $request->authenticate();

            // Regenera a sessão após autenticação bem-sucedida
            $request->session()->regenerate();

            // Log após autenticação bem-sucedida
            \Log::info('Login successful', [
                'user_id' => Auth::id(),
                'new_session_id' => session()->getId(),
                'is_authenticated' => Auth::check(),
                'cookies_after' => request()->cookies()->all()
            ]);

            // Força o salvamento da sessão
            session()->save();

            // Redireciona com mensagem de boas-vindas
            return redirect()
                ->intended(route('home', absolute: false))
                ->with('success', 'Welcome back ' . Auth::user()->first_name . ' ' . Auth::user()->last_name . '! You have successfully logged in.')
                ->withCookie(cookie()->forever('auth_check', 'true', null, null, false, false));

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Log detalhado de falha na validação
            \Log::warning('Login validation failed', [
                'email' => $request->input('email'),
                'errors' => $e->errors(),
                'session_status' => session()->all(),
                'cookies' => request()->cookies()->all()
            ]);

            return back()
                ->withErrors($e->errors())
                ->withInput($request->only('email'));

        } catch (\Exception $e) {
            // Log detalhado de erros inesperados
            \Log::error('Login error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'session_data' => session()->all(),
                'request_data' => $request->all()
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
