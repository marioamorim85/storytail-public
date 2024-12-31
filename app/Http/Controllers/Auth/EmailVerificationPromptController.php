<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailVerificationPromptController extends Controller
{
    /**
     * Apresenta o ecrã de verificação de email ou redireciona se já estiver verificado
     */
    public function __invoke(Request $request): RedirectResponse|View
    {
        // Verifica se o email já está verificado
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()
                ->intended(route('home', absolute: false))
                ->with('info', 'Your email is already verified.');
        }

        // Apresenta a view de verificação de email
        return view('auth.verify-email');
    }
}
