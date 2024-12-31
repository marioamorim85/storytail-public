<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class VerifyEmailController extends Controller
{
    /**
     * Marca o email do utilizador como verificado
     */
    public function __invoke(Request $request): RedirectResponse
    {
        try {
            // Obter o utilizador com base no ID da URL
            $user = User::find($request->route('id'));

            // Verifica se o utilizador existe
            if (!$user) {
                return redirect()
                    ->route('login')
                    ->with('error', 'Invalid verification link. Please request a new verification email.');
            }

            // Verifica se o email já foi validado
            if ($user->hasVerifiedEmail()) {
                return redirect()
                    ->route('login')
                    ->with('info', 'Your email is already verified. You can login to your account.');
            }

            // Verifica se o hash do email é válido
            if (!hash_equals(
                (string) $request->route('hash'),
                sha1($user->getEmailForVerification())
            )) {
                return redirect()
                    ->route('login')
                    ->with('error', 'Invalid or expired verification link. Please request a new one.');
            }

            // Marca o email como verificado
            $user->markEmailAsVerified();
            event(new Verified($user));

            return redirect()
                ->route('login')
                ->with('success', 'Email verified successfully! You can now login to your account.');

        } catch (\Exception $e) {
            // Regista o erro para debugging
            \Log::error('Erro na verificação de email: ' . $e->getMessage());

            return redirect()
                ->route('login')
                ->with('error', 'An error occurred during email verification. Please try again.');
        }
    }
}
