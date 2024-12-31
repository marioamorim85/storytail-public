<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class NewPasswordController extends Controller
{
    /**
     * Apresenta a view de reset da password
     */
    public function create(Request $request): View
    {
        return view('auth.reset-password', ['request' => $request]);
    }

    /**
     * Processa o pedido de nova password
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // Validação dos dados recebidos do formulário
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        try {
            // Tentativa de redefinir a password do utilizador
            $status = Password::reset(
                $request->only('email', 'password', 'password_confirmation', 'token'),
                function ($user) use ($request) {
                    $user->forceFill([
                        'password' => Hash::make($request->password),
                        'remember_token' => Str::random(60),
                    ])->save();

                    event(new PasswordReset($user));
                }
            );

            // Se a password foi redefinida com sucesso, redireciona para o login
            if ($status === Password::PASSWORD_RESET) {
                return redirect()
                    ->route('login')
                    ->with('success', 'Your password has been reset successfully! You can now login with your new password.');
            }

            // Se falhou a redefinição da password, retorna com erro
            return back()
                ->withInput($request->only('email'))
                ->with('error', __($status));

        } catch (\Exception $e) {
            // Regista o erro para debugging
            \Log::error('Erro ao redefinir password: ' . $e->getMessage());

            return back()
                ->withInput($request->only('email'))
                ->with('error', 'An unexpected error occurred. Please try again.');
        }
    }
}
