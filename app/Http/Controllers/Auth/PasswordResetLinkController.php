<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Apresenta a view para pedir o link de reset da password
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Processa o pedido de envio do link de reset da password
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // Validação do email
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        try {
            // Envia o link de reset da password para o utilizador
            // Após o envio, verifica a resposta e retorna a mensagem adequada
            $status = Password::sendResetLink(
                $request->only('email')
            );

            if ($status === Password::RESET_LINK_SENT) {
                return back()
                    ->with('success', 'A password reset link has been sent to your email address.');
            }

            // Se falhar o envio, retorna com mensagem de erro
            return back()
                ->withInput($request->only('email'))
                ->with('error', __($status));

        } catch (\Exception $e) {
            // Regista o erro para debugging
            \Log::error('Erro ao enviar link de reset: ' . $e->getMessage());

            return back()
                ->withInput($request->only('email'))
                ->with('error', 'An error occurred while sending the reset link. Please try again.');
        }
    }
}
