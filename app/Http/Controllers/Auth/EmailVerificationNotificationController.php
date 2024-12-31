<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Envia uma nova notificação de verificação de email
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            // Verifica se o email já está verificado
            if ($request->user()->hasVerifiedEmail()) {
                return redirect()
                    ->intended(route('home', absolute: false))
                    ->with('info', 'Your email is already verified.');
            }

            // Envia nova notificação de verificação
            $request->user()->sendEmailVerificationNotification();

            return back()
                ->with('success', 'A new verification link has been sent to your email address. Please check your inbox.');

        } catch (\Exception $e) {
            // Regista o erro para debugging
            \Log::error('Erro ao enviar verificação de email: ' . $e->getMessage());

            return back()
                ->with('error', 'Failed to send verification email. Please try again.');
        }
    }
}
