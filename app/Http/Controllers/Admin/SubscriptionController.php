<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\PremiumSubscriptionMail;
use App\Mail\SubscriptionRejectedMail;
use App\Models\SubscriptionApproval;
use App\Models\Subscription;
use App\Models\User;
use App\Models\Plan;
use App\Models\UserType;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class SubscriptionController extends Controller
{
    public function index()
    {
        $subscriptions = Subscription::with(['user', 'user.userType', 'plan', 'approvals'])
            ->orderBy('start_date', 'desc')
            ->get();

        $users = User::where('user_type_id', UserType::NORMAL_USER)
            ->whereDoesntHave('subscription')
            ->get();

        $plans = Plan::all();

        return view('admin.subscriptions.index', compact('subscriptions', 'users', 'plans'));
    }

    public function show($id)
    {
        $subscription = Subscription::with(['user', 'plan', 'approvals.admin'])->findOrFail($id);

        return view('admin.subscriptions.show', compact('subscription'));
    }

    public function create()
    {
        $users = User::where('user_type_id', UserType::NORMAL_USER)
            ->whereDoesntHave('subscription')
            ->get();
        $plans = Plan::all();

        return view('admin.subscriptions.create', compact('users', 'plans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'plan_id' => 'required|exists:plans,id',
            'start_date' => 'required|date|before_or_equal:today',
            'end_date' => 'nullable|date|after:start_date',
        ]);

        $user = User::findOrFail($request->user_id);

        if ($user->isAdmin()) {
            return redirect()->back()->with('error', 'Cannot create subscription for admin users.')->withInput();
        }

        if ($user->subscription) {
            return redirect()->back()->with('error', 'User already has an active subscription.')->withInput();
        }

        try {
            DB::beginTransaction();

            Subscription::create([
                'user_id' => $request->user_id,
                'plan_id' => $request->plan_id,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
            ]);

            DB::commit();

            return redirect()
                ->route('admin.users.subscriptions.list')
                ->with('success', 'Subscription created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating subscription: ' . $e->getMessage());

            return redirect()->back()->with('error', 'Failed to create subscription. Please try again.')->withInput();
        }
    }

    public function edit($id)
    {
        $subscription = Subscription::with(['user.userType', 'plan'])->findOrFail($id);

        $users = User::where('user_type_id', UserType::NORMAL_USER)
            ->where(function ($query) use ($subscription) {
                $query->whereDoesntHave('subscription')
                    ->orWhere('id', $subscription->user_id);
            })
            ->get();
        $plans = Plan::all();

        return view('admin.subscriptions.edit', compact('subscription', 'users', 'plans'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'plan_id' => 'required|exists:plans,id',
            'start_date' => 'required|date|before_or_equal:today',
            'end_date' => 'nullable|date|after:start_date',
        ]);

        try {
            DB::beginTransaction();

            $subscription = Subscription::findOrFail($id);
            $subscription->update($request->only(['user_id', 'plan_id', 'start_date', 'end_date']));

            DB::commit();

            return redirect()
                ->route('admin.users.subscriptions.list')
                ->with('success', 'Subscription updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating subscription: ' . $e->getMessage());

            return redirect()->back()->with('error', 'Failed to update subscription. Please try again.')->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $subscription = Subscription::findOrFail($id);
            $subscription->delete();

            DB::commit();

            return redirect()
                ->route('admin.users.subscriptions.list')
                ->with('success', 'Subscription deleted successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting subscription: ' . $e->getMessage());

            return redirect()->back()->with('error', 'Failed to delete subscription. Please try again.');
        }
    }

    public function moderateSubscription(Request $request, $id)
    {
        try {
            $request->validate([
                'action' => 'required|in:approve,reject',
                'notes' => 'nullable|string|max:500',
            ]);

            DB::beginTransaction();

            // Localizar a aprovação pendente e a subscrição associada
            $approval = SubscriptionApproval::findOrFail($id);
            $currentSubscription = $approval->subscription;

            if (!$currentSubscription) {
                throw new \Exception('Subscription not found.');
            }

            // Verificar se o pedido já foi resolvido
            if ($approval->isResolved()) {
                return redirect()
                    ->back()
                    ->with('error', 'This subscription request has already been resolved.');
            }

            // Atualizar o pedido atual como "resolvido"
            $approval->update([
                'status' => SubscriptionApproval::STATUS_RESOLVED,
                'user_id' => auth()->id(),
                'approval_date' => now(),
                'notes' => $request->notes ?: ($request->action === 'approve'
                    ? 'Request for Premium subscription approved.'
                    : 'Request for Premium subscription rejected.'),
            ]);

            if ($request->action === 'approve') {
                // Encerrar a subscrição atual
                $currentSubscription->update([
                    'status' => 'inactive',
                    'end_date' => now(),
                ]);

                // Criar uma nova subscrição Premium
                $newSubscription = Subscription::create([
                    'user_id' => $currentSubscription->user_id,
                    'plan_id' => Plan::PREMIUM,
                    'status' => 'active',
                    'start_date' => now(),
                    'is_renewable' => true,
                ]);

                // Registar nova entrada na tabela de aprovações para a nova subscrição
                SubscriptionApproval::create([
                    'subscription_id' => $newSubscription->id,
                    'user_id' => auth()->id(),
                    'status' => 'approved',
                    'plan_name' => 'Premium',
                    'approval_date' => now(),
                    'notes' => 'Subscription upgraded to Premium by administrator.',
                ]);

                // Enviar email de confirmação ao utilizador
                $user = $newSubscription->user;
                Mail::to($user->email)->send(new PremiumSubscriptionMail($user));

            } else {
                // Enviar email de rejeição
                $user = $currentSubscription->user;
                Mail::to($user->email)->send(new SubscriptionRejectedMail($user));
            }

            DB::commit();

            return redirect()
                ->route('admin.users.subscriptions.list')
                ->with('success', 'Subscription request processed successfully.');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error in subscription moderation: ' . $e->getMessage());

            return redirect()
                ->back()
                ->with('error', 'Failed to process the subscription request. Please try again.');
        }
    }
}
