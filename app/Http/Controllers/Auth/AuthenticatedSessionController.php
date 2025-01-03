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
            // Log inicial com informações do ambiente
            \Log::info('Login environment check', [
                'app_env' => config('app.env'),
                'app_url' => config('app.url'),
                'session_config' => config('session'),
                'server_vars' => request()->server->all()
            ]);

            // Log detalhado da tentativa de login
            \Log::info('Login attempt details', [
                'email' => $request->input('email'),
                'ip' => $request->ip(),
                'session_exists' => $request->hasSession(),
                'session_id' => session()->getId(),
                'session_driver' => config('session.driver'),
                'session_domain' => config('session.domain'),
                'cookies' => request()->cookies()->all(),
                'headers' => request()->headers->all(),
                'user_agent' => $request->userAgent(),
                'current_session_data' => session()->all()
            ]);

            // Tenta autenticar antes de regenerar tokens
            $request->authenticate();

            // Log após autenticação, antes da regeneração
            \Log::info('Authentication successful, pre-session regeneration', [
                'user_id' => Auth::id(),
                'auth_check' => Auth::check(),
                'session_id' => session()->getId(),
                'session_data' => session()->all()
            ]);

            // Regenera sessão e token
            $request->session()->regenerate();
            $request->session()->regenerateToken();

            // Log após regeneração da sessão
            \Log::info('Session regenerated', [
                'new_session_id' => session()->getId(),
                'user_still_auth' => Auth::check(),
                'new_session_data' => session()->all(),
                'new_cookies' => request()->cookies()->all()
            ]);

            // Força salvamento da sessão
            session()->save();

            // Log final antes do redirecionamento
            \Log::info('Preparing redirect', [
                'intended_url' => session()->get('url.intended'),
                'route_home' => route('home', absolute: false),
                'user_data' => Auth::user()->only(['id', 'first_name', 'last_name', 'email'])
            ]);

            return redirect()
                ->intended(route('home', absolute: false))
                ->with('success', 'Welcome back ' . Auth::user()->first_name . ' ' . Auth::user()->last_name . '! You have successfully logged in.')
                ->withHeaders([
                    'Cache-Control' => 'no-cache, no-store, must-revalidate',
                    'Pragma' => 'no-cache',
                    'Expires' => '0'
                ])
                ->withCookie(cookie()->forever('auth_check', 'true', null, config('session.domain'), true, true));

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::warning('Validation failed', [
                'email' => $request->input('email'),
                'errors' => $e->errors(),
                'session_status' => session()->all(),
                'cookies' => request()->cookies()->all(),
                'validation_exception' => [
                    'message' => $e->getMessage(),
                    'code' => $e->getCode(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]
            ]);

            return back()
                ->withErrors($e->errors())
                ->withInput($request->only('email'));

        } catch (\Exception $e) {
            \Log::error('Authentication error', [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'session_data' => session()->all(),
                'request_data' => $request->all(),
                'auth_status' => Auth::check(),
                'current_url' => $request->url(),
                'previous_url' => url()->previous(),
                'server_info' => [
                    'php_version' => PHP_VERSION,
                    'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'unknown'
                ]
            ]);

            return back()
                ->withErrors(['email' => 'An unexpected error occurred. Please try again.'])
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
