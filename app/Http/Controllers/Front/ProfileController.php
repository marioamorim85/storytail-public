<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionApproval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;



class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function showProfilePage(Request $request)
    {
        $user = $request->user()->load('ranking');

        if (!$user) {
            return redirect()->route('login')->with('error', 'You must be logged in.');
        }

        return view('manage-acount.profile', compact('user'));
    }


    public function edit(Request $request)
    {
        $user = $request->user()->load('ranking');

        if (!$user) {
            return redirect()->route('login')->with('error', 'You must be logged in to access this page.');
        }

        return view('manage-acount.profile', compact('user'));
    }


    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    {
        try {
            // Valida todos os campos do formulário
            $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $request->user()->id,
                'birth_date' => 'required|date|before:today',
                'old_password' => 'nullable|string|min:8',
                'new_password' => 'nullable|string|min:8|confirmed',
                'user_photo_url' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            $user = $request->user();

            // Verifica e atualiza a senha se fornecida
            if ($request->filled('old_password')) {
                if (!Hash::check($request->old_password, $user->password)) {
                    return back()->withErrors(['old_password' => 'The old password is incorrect.']);
                }
                $user->password = Hash::make($request->new_password);
            }

            // Processa o upload da foto se uma nova imagem foi enviada
            if ($request->hasFile('user_photo_url')) {
                try {
                    // Remove a foto antiga se existir
                    if ($user->user_photo_url) {
                        Storage::disk('public')->delete($user->user_photo_url);
                    }

                    // Armazena a nova foto
                    $path = $request->file('user_photo_url')->store('user-photos', 'public');

                    if (!$path) {
                        throw new \Exception('Failed to store the photo.');
                    }

                    $user->user_photo_url = $path;

                } catch (\Exception $e) {
                    // Captura erros específicos do processamento da foto
                    return back()
                        ->withInput()
                        ->with('error', 'Failed to upload photo: ' . $e->getMessage());
                }
            }

            // Atualiza as informações básicas do usuário
            $user->update([
                'first_name' => $request->input('first_name'),
                'last_name' => $request->input('last_name'),
                'email' => $request->input('email'),
                'birth_date' => $request->input('birth_date'),
                'user_photo_url' => $user->user_photo_url
            ]);

            return back()->with('success', 'Profile updated successfully!');

        } catch (\Exception $e) {
            // Captura qualquer outro erro que possa ocorrer durante o processo
            return back()
                ->withInput()
                ->with('error', 'An error occurred while updating your profile: ' . $e->getMessage());
        }
    }

    public function removePhoto()
    {
        try {
            $user = auth()->user();

            if ($user->user_photo_url) {
                Storage::disk('public')->delete($user->user_photo_url);
                $user->update(['user_photo_url' => null]);
            }

            return redirect()->back()->with('success', 'Photo removed successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to remove photo.');
        }
    }


    /**
     * Delete the user's account.
     */
    public function destroy(Request $request)
    {

        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();


        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/')->with('success', 'Your account has been deleted successfully.');
    }
}
