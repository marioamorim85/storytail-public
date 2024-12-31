<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * Atualiza a password do utilizador
     */
    public function update(Request $request): RedirectResponse
    {
        try {
            // Validação dos dados com bag específico para atualização de password
            $validated = $request->validateWithBag('updatePassword', [
                'current_password' => ['required', 'current_password'],
                'password' => ['required', Password::defaults(), 'confirmed'],
            ]);

            // Atualiza a password do utilizador
            $request->user()->update([
                'password' => Hash::make($validated['password']),
            ]);

            // Redireciona com mensagem de sucesso
            return back()->with('success', 'Your password has been updated successfully!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Erro de validação
            return back()->with('error', 'Please check your current password and try again.');

        } catch (\Exception $e) {
            // Regista o erro para debugging
            \Log::error('Erro ao atualizar password: ' . $e->getMessage());

            return back()->with('error', 'An error occurred while updating your password. Please try again.');
        }
    }
}
