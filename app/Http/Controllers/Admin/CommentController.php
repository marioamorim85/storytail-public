<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\CommentApprovedMail;
use App\Mail\CommentRejectedMail;
use App\Models\Book;
use App\Models\Comment;
use App\Models\CommentModeration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class CommentController extends Controller
{
    public function index()
    {
        // Carrega os comentários e a moderação mais recente
        $comments = Comment::with([
            'user',
            'book',
            'moderation' => function ($query) {
                $query->latest(); // Garante que o mais recente é carregado
            },
        ])->latest()->get();

        $books = Book::all();

        return view('admin.comments.index', compact('comments', 'books'));
    }


    public function show($id)
    {
        $comment = Comment::with(['user', 'book', 'moderation.user'])->findOrFail($id);

        return view('admin.comments.show', compact('comment'));
    }

    public function edit($id)
    {
        $comment = Comment::with(['user', 'book', 'moderation'])->findOrFail($id);

        return view('admin.comments.edit', compact('comment'));
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'status' => 'required|in:pending,approved,rejected',
                'notes' => 'nullable|string|max:500',
            ]);

            DB::beginTransaction();

            $comment = Comment::findOrFail($id);

            // Determina a mensagem automática se as notas não forem fornecidas
            $defaultNotes = match ($request->status) {
                'approved' => 'This comment has been reviewed and approved.',
                'rejected' => 'This comment has been reviewed and rejected due to inappropriate content.',
                'pending' => 'This comment is pending review.',
            };

            // Atualiza ou cria a moderação
            $comment->moderation()->updateOrCreate(
                ['comment_id' => $comment->id],
                [
                    'user_id' => auth()->id(),
                    'status' => $request->status,
                    'moderation_date' => now(),
                    'notes' => $request->notes ?? $defaultNotes, // Garantindo a nota padrão se não fornecida
                ]
            );

            DB::commit();

            return redirect()
                ->route('admin.users.comments.list')
                ->with('success', 'Comment updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error updating comment: ' . $e->getMessage());

            return redirect()
                ->back()
                ->with('error', 'Failed to update comment. Please try again.');
        }
    }


    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $comment = Comment::findOrFail($id);
            $comment->delete();

            DB::commit();

            return redirect()
                ->route('admin.users.comments.list')
                ->with('success', "Comment has been deleted successfully!");
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error deleting comment: ' . $e->getMessage());

            return redirect()
                ->route('admin.users.comments.list')
                ->with('error', 'Failed to delete comment. Please try again.');
        }
    }

    public function moderate(Request $request, $id)
    {
        try {
            $request->validate([
                'action' => 'required|in:approve,reject',
                'notes' => 'nullable|string|max:500',
            ]);

            DB::beginTransaction();

            $comment = Comment::findOrFail($id);

            $status = $request->action === 'approve'
                ? CommentModeration::STATUS_APPROVED
                : CommentModeration::STATUS_REJECTED;

            // Mensagem automática para notas
            $defaultNotes = $request->action === 'approve'
                ? 'This comment has been reviewed and approved.'
                : 'This comment has been reviewed and rejected due to inappropriate content.';

            $comment->moderation()->updateOrCreate(
                ['comment_id' => $comment->id],
                [
                    'user_id' => auth()->id(),
                    'status' => $status,
                    'moderation_date' => now(),
                    'notes' => $request->notes ?: $defaultNotes,
                ]
            );

            // Enviar email baseado na ação
            $user = $comment->user;
            if ($request->action === 'approve') {
                Mail::to($user->email)->send(new CommentApprovedMail($user, $comment));
            } else {
                Mail::to($user->email)->send(new CommentRejectedMail($user, $comment));
            }

            DB::commit();

            $action = ucfirst($request->action);
            return redirect()
                ->route('admin.users.comments.list')
                ->with('success', "Comment has been " . strtolower($action) . "ed successfully!");
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error moderating comment: ' . $e->getMessage());

            return redirect()
                ->back()
                ->with('error', 'Failed to moderate comment. Please try again.');
        }
    }
}
