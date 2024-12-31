<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionApproval;
use App\Models\User;
use App\Models\UserType;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'birth_date' => ['required', 'date', 'before:today', 'after:1900-01-01']
        ]);

        try {
            DB::beginTransaction();

            // Criar usuário
            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'birth_date' => $request->birth_date,
                'user_type_id' => UserType::NORMAL_USER, // Usando a constante
                'status' => 'active'
            ]);

            // Criar subscrição free
            $subscription = Subscription::create([
                'user_id' => $user->id,
                'plan_id' => Plan::FREE, // Usando a constante
                'start_date' => now()
            ]);

            // Criar entrada na tabela de aprovações para subscrição Free
            SubscriptionApproval::create([
                'subscription_id' => $subscription->id,
                'user_id' => null, // Define como null para indicar "System"
                'status' => 'approved', // Subscrição Free é automaticamente aprovada
                'plan_name' => 'Free', // Nome do plano armazenado
                'notes' => 'Automatically approved Free subscription during registration.',
                'approval_date' => now(),
            ]);


            DB::commit();

            event(new Registered($user));

            return redirect()
                ->route('login')
                ->with('success', 'Registration successful! Please check your email to verify your account.');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Registration failed. Please try again.');
        }
    }
}
