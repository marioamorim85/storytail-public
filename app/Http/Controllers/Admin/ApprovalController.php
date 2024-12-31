<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\CommentApprovedMail;
use App\Mail\CommentRejectedMail;
use App\Mail\PremiumSubscriptionMail;
use App\Mail\SubscriptionRejectedMail;
use App\Models\CommentModeration;
use App\Models\SubscriptionApproval;
use App\Models\Subscription;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ApprovalController extends Controller
{
    // Gestão de Moderação de Comentários
    public function comments()
    {
        $comments = CommentModeration::where('status', CommentModeration::STATUS_PENDING)
            ->with(['comment.user', 'comment.book', 'user'])
            ->latest()
            ->paginate(15);

        return view('admin.approvals.comments', compact('comments'));
    }

    public function updateComment(Request $request, $id)
    {
        try {
            $request->validate([
                'action' => 'required|in:approve,reject,pending',
                'notes' => 'nullable|string|max:500',
            ]);

            DB::beginTransaction();

            $commentModeration = CommentModeration::findOrFail($id);

            if (!$commentModeration->isPending() && $request->action !== 'pending') {
                throw new \Exception('This comment has already been moderated.');
            }

            $status = match ($request->action) {
                'approve' => CommentModeration::STATUS_APPROVED,
                'reject' => CommentModeration::STATUS_REJECTED,
                'pending' => CommentModeration::STATUS_PENDING,
            };

            $notes = $request->notes ?: match ($request->action) {
                'approve' => 'This comment has been reviewed and approved.',
                'reject' => 'This comment has been reviewed and rejected due to inappropriate content.',
                'pending' => 'This comment has been marked as pending for further review.',
            };

            $commentModeration->update([
                'status' => $status,
                'moderation_date' => $request->action === 'pending' ? null : now(),
                'user_id' => $request->action === 'pending' ? null : auth()->id(),
                'notes' => $notes,
            ]);

            $commentModeration->comment->update(['status' => $status]);

            // Enviar email baseado na ação
            if ($request->action !== 'pending') {
                $user = $commentModeration->comment->user;
                $comment = $commentModeration->comment;

                if ($request->action === 'approve') {
                    Mail::to($user->email)->send(new CommentApprovedMail($user, $comment));
                } elseif ($request->action === 'reject') {
                    Mail::to($user->email)->send(new CommentRejectedMail($user, $comment));
                }
            }

            DB::commit();

            $action = ucfirst($request->action);
            return redirect()
                ->route('admin.approvals.comments')
                ->with('success', "Comment has been " . strtolower($action) . "ed successfully!");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating comment moderation', [
                'message' => $e->getMessage(),
                'stack' => $e->getTraceAsString(),
            ]);
            return redirect()
                ->back()
                ->with('error', 'Failed to moderate the comment. Please try again.');
        }
    }


    // Gestão de Moderação de Subscrições
    public function subscriptions()
    {
        $subscriptions = SubscriptionApproval::with(['subscription.user', 'subscription.plan'])
            ->where('status', SubscriptionApproval::STATUS_PENDING)
            ->latest()
            ->get();

        return view('admin.approvals.subscriptions', compact('subscriptions'));
    }


    public function updateSubscription(Request $request, $id)
    {
        try {
            if (!auth()->user()->isAdmin()) {
                throw new \Exception('Only administrators can approve/reject subscriptions.');
            }

            $request->validate([
                'action' => 'required|in:approve,reject',
                'notes' => 'nullable|string|max:500',
            ]);

            DB::beginTransaction();

            // Buscar a aprovação pendente e a subscrição associada
            $approval = SubscriptionApproval::findOrFail($id);
            $subscription = $approval->subscription;

            if (!$subscription) {
                throw new \Exception('Subscription not found.');
            }

            // Verificar se o pedido já foi resolvido
            if ($approval->isResolved()) {
                return redirect()
                    ->back()
                    ->with('error', 'This subscription request has already been resolved.');
            }

            // Determinar o plano para registar o histórico
            $currentPlanName = 'Free';
            $newPlanName = $request->action === 'approve' ? 'Premium' : $currentPlanName;

            // Atualizar o pedido atual como resolvido
            $approval->update([
                'status' => SubscriptionApproval::STATUS_RESOLVED,
                'user_id' => auth()->id(),
                'approval_date' => now(),
                'plan_name' => "{$currentPlanName} -> {$newPlanName}",
                'notes' => $request->notes ?: ($request->action === 'approve'
                    ? 'Request for Premium subscription approved.'
                    : 'Request for Premium subscription rejected.'),
            ]);

            // Se aprovado, finalizar a subscrição atual e criar uma nova
            if ($request->action === 'approve') {
                // Finalizar a subscrição atual
                $subscription->update([
                    'status' => 'inactive',
                    'end_date' => now(),
                ]);

                // Criar uma nova subscrição Premium
                $newSubscription = Subscription::create([
                    'user_id' => $subscription->user_id,
                    'plan_id' => Plan::PREMIUM,
                    'status' => 'active',
                    'start_date' => now(),
                    'is_renewable' => true,
                ]);

                // Registar a aprovação no histórico
                SubscriptionApproval::create([
                    'subscription_id' => $newSubscription->id,
                    'user_id' => auth()->id(),
                    'status' => SubscriptionApproval::STATUS_APPROVED,
                    'plan_name' => 'Premium',
                    'notes' => 'Subscription upgraded to premium by administrator.',
                    'approval_date' => now(),
                ]);

                // Enviar email de confirmação ao utilizador
                $user = $newSubscription->user;
                Mail::to($user->email)->send(new PremiumSubscriptionMail($user));
            } else {
                // Enviar email de rejeição ao utilizador
                $user = $subscription->user;
                Mail::to($user->email)->send(new SubscriptionRejectedMail($user));
            }

            DB::commit();

            $action = ucfirst($request->action);
            return redirect()
                ->route('admin.approvals.subscriptions')
                ->with('success', "Subscription request has been {$action}ed successfully.");
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error processing subscription', ['message' => $e->getMessage()]);
            return redirect()->back()->with('error', $e->getMessage());
        }
    }



    // Histórico de Aprovações
    public function history()
    {
        $commentHistory = CommentModeration::where('status', '!=', CommentModeration::STATUS_PENDING)
            ->with(['comment.user', 'comment.book', 'user'])
            ->latest()
            ->take(50)
            ->get();

        $subscriptionHistory = SubscriptionApproval::with(['subscription.user', 'subscription.plan', 'admin'])
            ->orderBy('created_at', 'desc') // Ordena pela data de criação em ordem decrescente
            ->get();

        return view('admin.approvals.history', compact('commentHistory', 'subscriptionHistory'));
    }
}
