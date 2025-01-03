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
            \Log::info('Login attempt', [
                'email' => $request->input('email'),
                'ip' => $request->ip(),
                'session_id' => session()->getId(),
                'session_driver' => config('session.driver'),
                'session_domain' => config('session.domain'),
                'cookies' => $request->cookie(), // Corrigido aqui
                'headers' => $request->headers->all()
            ]);

            // Tenta autenticar
            $request->authenticate();

            // Regenera a sessão após autenticação bem-sucedida
            $request->session()->regenerate();

            // Log após autenticação bem-sucedida
            \Log::info('Login successful', [
                'user_id' => Auth::id(),
                'new_session_id' => session()->getId(),
                'is_authenticated' => Auth::check()
            ]);

            // Força o salvamento da sessão
            session()->save();

            return redirect()
                ->intended(route('home', absolute: false))
                ->with('success', 'Welcome back ' . Auth::user()->first_name . ' ' . Auth::user()->last_name . '!')
                ->withHeaders([
                    'Cache-Control' => 'no-cache, no-store, must-revalidate'
                ]);

        } catch (\Exception $e) {
            \Log::error('Authentication error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
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
