<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\CommentModeration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommentController extends Controller
{
    public function store(Request $request, $bookId)
    {
        try {
            $readProgress = auth()->user()->booksRead()
                ->where('book_id', $bookId)
                ->take(5)
                ->first()?->pivot?->progress ?? 0;

            if ($readProgress < 90) {
                return back()->with('error', 'You need to read at least 90% of the book to add a comment.');
            }

            $request->validate([
                'rating' => 'required|integer|min:1|max:5',
                'comment_text' => 'required|string|max:500',
            ]);

            // Verificar se já existe um comentário pendente para este livro
            $existingComment = Comment::where('user_id', auth()->id())
                ->where('book_id', $bookId)
                ->whereHas('moderation', function ($query) {
                    $query->where('status', CommentModeration::STATUS_PENDING);
                })
                ->first();

            if ($existingComment) {
                return back()->with('info', 'Your comment is already pending approval.');
            }

            DB::beginTransaction();

            // Criar o comentário
            $comment = Comment::create([
                'user_id' => auth()->id(),
                'book_id' => $bookId,
                'comment_text' => $request->comment_text,
                'rating' => $request->rating,
            ]);

            // Criar a moderação
            $comment->moderation()->create([
                'user_id' => auth()->id(),
                'status' => CommentModeration::STATUS_PENDING,
                'moderation_date' => now(),
                'notes' => 'Comment pending review',
            ]);

            // Salvar ou atualizar o rating diretamente na tabela de progresso
            DB::table('book_user_read')
                ->updateOrInsert(
                    [
                        'book_id' => $bookId,
                        'user_id' => auth()->id(),
                    ],
                    [
                        'rating' => $request->rating,
                        'updated_at' => now(),
                    ]
                );

            DB::commit();

            return back()->with('success', 'Your comment and rating have been submitted.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating comment or saving rating: ' . $e->getMessage());

            return back()->with('error', 'An error occurred while submitting your comment. Please try again.');
        }
    }


}
