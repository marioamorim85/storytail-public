<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionApproval;
use App\Models\User;
use App\Models\UserType;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with(['userType', 'subscription.plan'])->get();
        $userTypes = UserType::all();
        $plans = Plan::all();

        // Prepare contagens
        $planCounts = $users->groupBy('subscription.plan_id')
            ->map->count();

        return view('admin.users.index', compact('users', 'userTypes', 'plans', 'planCounts'));
    }

    public function create()
    {
        $userTypes = UserType::all();
        $plans = Plan::all();
        return view('admin.users.create', compact('userTypes', 'plans'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:8',
                'user_type_id' => 'required|exists:user_types,id',
                'user_photo_url' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
                'birth_date' => 'nullable|date',
                'status' => 'required|in:active,suspended,inactive',
                'plan_id' => 'nullable|exists:plans,id'
            ]);

            DB::beginTransaction();

            // Upload da foto do usuário
            $photoPath = null;
            if ($request->hasFile('user_photo_url')) {
                $photoPath = $request->file('user_photo_url')->store('users', 'public');
            }

            // Criar usuário
            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'user_type_id' => $request->user_type_id ?? UserType::NORMAL_USER,
                'user_photo_url' => $photoPath,
                'birth_date' => $request->birth_date,
                'status' => $request->status,
            ]);

            // Criar subscrição
            $subscription = Subscription::create([
                'user_id' => $user->id,
                'plan_id' => $request->plan_id ?? Plan::FREE,
                'start_date' => now(),
            ]);

            // Criar registo na aprovação para qualquer plano
            $action = $request->plan_id == Plan::PREMIUM ? 'created with premium plan' : 'created with free plan';

            SubscriptionApproval::create([
                'subscription_id' => $subscription->id,
                'user_id' => auth()->id(),
                'status' => 'approved',
                'approval_date' => now(),
                'notes' => "Subscription " . $action . " by administrator."
            ]);

            DB::commit();

            return redirect()
                ->route('admin.users.list')
                ->with('success', "User \"{$user->getFullName()}\" has been created successfully!");

        } catch (\Exception $e) {
            DB::rollBack();

            // Se houve upload de foto, remove
            if (isset($photoPath)) {
                Storage::disk('public')->delete($photoPath);
            }

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        try {
            $user = User::with(['userType', 'subscription.plan', 'booksRead', 'comments'])
                ->findOrFail($id);
            return view('admin.users.show', compact('user'));
        } catch (ModelNotFoundException $e) {
            return redirect()
                ->route('admin.users.list')
                ->with('error', 'User not found.');
        }
    }

    public function edit($id)
    {
        try {
            $user = User::with(['userType', 'subscription.plan'])->findOrFail($id);
            $userTypes = UserType::all();
            $plans = Plan::all();
            return view('admin.users.edit', compact('user', 'userTypes', 'plans'));
        } catch (ModelNotFoundException $e) {
            return redirect()
                ->route('admin.users.list')
                ->with('error', 'User not found.');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);

            // Impedir mudança de tipo se for o último admin
            if ($user->user_type_id === UserType::ADMIN &&
                $request->user_type_id !== UserType::ADMIN &&
                User::where('user_type_id', UserType::ADMIN)->count() === 1) {
                throw new \Exception('Cannot change type of last admin user.');
            }

            $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $id,
                'password' => 'nullable|min:8',
                'user_type_id' => 'required|exists:user_types,id',
                'user_photo_url' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
                'birth_date' => 'nullable|date',
                'status' => 'required|in:active,suspended,inactive',
                'plan_id' => 'nullable|exists:plans,id',
            ]);

            DB::beginTransaction();

            $data = $request->only([
                'first_name',
                'last_name',
                'email',
                'birth_date',
                'user_type_id',
                'status',
            ]);

            // Atualizar senha se fornecida
            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            // Upload de nova foto
            if ($request->hasFile('user_photo_url')) {
                if ($user->user_photo_url) {
                    Storage::disk('public')->delete($user->user_photo_url);
                }
                $data['user_photo_url'] = $request->file('user_photo_url')->store('users', 'public');
            }

            $user->update($data);

            // Verificar subscrição atual
            $currentSubscription = $user->subscription;
            $newPlanId = $request->plan_id ?? Plan::FREE;

            if (!$currentSubscription || $currentSubscription->plan_id != $newPlanId) {
                // Encerrar a subscrição atual (se houver)
                if ($currentSubscription) {
                    $currentSubscription->update([
                        'end_date' => now(),
                        'status' => 'inactive',
                    ]);
                }

                // Criar nova subscrição
                $newSubscription = Subscription::create([
                    'user_id' => $user->id,
                    'plan_id' => $newPlanId,
                    'start_date' => now(),
                    'status' => 'active',
                ]);

                // Criar registo de aprovação para mudança de plano
                $action = $newPlanId == Plan::PREMIUM ? 'upgraded to premium' : 'downgraded to free';

                SubscriptionApproval::create([
                    'subscription_id' => $newSubscription->id,
                    'user_id' => auth()->id(),
                    'status' => 'approved',
                    'plan_name' => $newSubscription->plan->name,
                    'approval_date' => now(),
                    'notes' => "Subscription {$action} by administrator.",
                ]);
            }

            DB::commit();

            return redirect()
                ->route('admin.users.list')
                ->with('success', "User \"{$user->getFullName()}\" has been updated successfully!");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }


    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);

            // Impedir exclusão de admin
            if ($user->isAdmin() && User::admins()->count() === 1) {
                throw new \Exception('Cannot delete the last admin user.');
            }

            DB::beginTransaction();

            // Remover foto do usuário
            if ($user->user_photo_url) {
                Storage::disk('public')->delete($user->user_photo_url);
            }

            $userName = $user->getFullName();

            // A subscrição será excluída automaticamente devido ao onDelete('cascade')
            $user->delete();

            DB::commit();

            return redirect()
                ->route('admin.users.list')
                ->with('success', "User \"$userName\" has been deleted successfully!");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->route('admin.users.list')
                ->with('error', 'Failed to delete user: ' . $e->getMessage());
        }
    }

    public function removePhoto($id)
    {
        try {
            DB::beginTransaction();

            $user = User::findOrFail($id);

            if ($user->user_photo_url) {
                Storage::disk('public')->delete($user->user_photo_url);
                $user->update(['user_photo_url' => null]);
            }

            DB::commit();

            return redirect()
                ->route('admin.users.edit', $id)
                ->with('success', 'Photo has been removed successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->route('admin.users.edit', $id)
                ->with('error', 'Failed to remove photo: ' . $e->getMessage());
        }
    }
}
