<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\SubscriptionApproval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PlanController extends Controller
{
    public function downgrade()
    {
        try {
            $user = auth()->user();
            $subscription = $user->subscription;

            if (!$subscription) {
                return back()->with('error', 'No active subscription found.');
            }

            if ($subscription->plan_id === Plan::FREE) {
                return back()->with('error', 'You are already on the Free plan.');
            }

            DB::transaction(function() use ($subscription, $user) {
                // Finaliza a subscrição premium atual
                $subscription->update([
                    'status' => 'inactive',
                    'end_date' => now(),
                ]);

                // Cria uma nova subscrição Free
                $newSubscription = Subscription::create([
                    'user_id' => $user->id,
                    'plan_id' => Plan::FREE,
                    'status' => 'active',
                    'start_date' => now(),
                    'is_renewable' => true,
                ]);

                // Regista a alteração no histórico
                SubscriptionApproval::create([
                    'subscription_id' => $newSubscription->id,
                    'user_id' => auth()->id(),
                    'status' => 'approved',
                    'plan_name' => 'Free',
                    'notes' => 'User downgraded to Free plan.',
                    'approval_date' => now(),
                ]);
            });

            return back()->with('success', 'Successfully downgraded to Free plan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to downgrade subscription. Please try again.');
        }
    }

    public function requestPremium()
    {
        try {
            $user = auth()->user();
            $subscription = $user->subscription;

            if (!$subscription) {
                return back()->with('error', 'No active subscription found.');
            }

            // Verifica se já existe um pedido pendente
            $pendingRequest = SubscriptionApproval::whereHas('subscription', function($query) use ($user) {
                $query->where('user_id', $user->id);
            })
                ->where('status', 'pending')
                ->exists();

            if ($pendingRequest) {
                return back()->with('info', 'You already have a pending Premium request.');
            }

            // Criar novo pedido de aprovação
            SubscriptionApproval::create([
                'subscription_id' => $subscription->id,
                'status' => 'pending',
                'notes' => 'User requested Premium access.',
                'plan_name' => 'Premium',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return back()->with('success', 'Premium access request submitted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to submit Premium request. Please try again.');
        }
    }
}
