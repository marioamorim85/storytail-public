<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\Comment;
use App\Models\CommentModeration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * @OA\Tag(
 *     name="Comments",
 *     description="APIs relacionadas com comentários dos livros"
 * )
 */
class CommentController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/books/{bookId}/comments",
     *     summary="Comentários de um livro",
     *     description="Retorna todos os comentários aprovados de um livro",
     *     tags={"Comments"},
     *     @OA\Parameter(
     *         name="bookId",
     *         in="path",
     *         required=true,
     *         description="ID do livro",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de comentários",
     *         @OA\JsonContent(
     *             @OA\Property(property="book_title", type="string", example="O Grande Livro"),
     *             @OA\Property(
     *                 property="comments",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="comment_text", type="string", example="Excelente livro!"),
     *                     @OA\Property(property="rating", type="integer", example=5),
     *                     @OA\Property(property="created_at", type="string", format="date-time"),
     *                     @OA\Property(property="moderation_status", type="string", example="approved"),
     *                     @OA\Property(
     *                         property="user",
     *                         type="object",
     *                         @OA\Property(property="id", type="integer"),
     *                         @OA\Property(property="name", type="string")
     *                     )
     *                 )
     *             )
     *         )
     *     )
     * ),
     * @OA\Post(
     *     path="/api/books/{bookId}/comments",
     *     summary="Criar comentário",
     *     description="Adiciona um novo comentário ao livro",
     *     tags={"Comments"},
     *     @OA\Parameter(
     *         name="bookId",
     *         in="path",
     *         required=true,
     *         description="ID do livro",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="rating", type="integer", minimum=1, maximum=5),
     *             @OA\Property(property="comment_text", type="string", maxLength=500)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Comentário criado com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Comentário submetido com sucesso")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Erro de validação ou progresso insuficiente",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    public function getBookComments($bookId)
    {
        try {
            $book = Book::with(['comments' => function($query) {
                $query->whereHas('moderation', function($q) {
                    $q->where('status', CommentModeration::STATUS_APPROVED);
                })
                    ->with(['user', 'moderation'])
                    ->orderBy('created_at', 'desc');
            }])->findOrFail($bookId);

            return response()->json([
                'book_title' => $book->title,
                'comments' => $book->comments->map(function($comment) {
                    return [
                        'id' => $comment->id,
                        'comment_text' => $comment->comment_text,
                        'rating' => $comment->rating,
                        'created_at' => $comment->created_at,
                        'moderation_status' => $comment->moderation->status,
                        'user' => [
                            'id' => $comment->user->id,
                            'name' => $comment->user->name
                        ]
                    ];
                })
            ]);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao buscar comentários'], 500);
        }
    }

    public function store(Request $request, $bookId)
    {
        try {
            $readProgress = auth()->user()->booksRead()
                ->where('book_id', $bookId)
                ->first()?->pivot?->progress ?? 0;

            if ($readProgress < 90) {
                return response()->json([
                    'message' => 'É necessário ler pelo menos 90% do livro para comentar.'
                ], 400);
            }

            $request->validate([
                'rating' => 'required|integer|min:1|max:5',
                'comment_text' => 'required|string|max:500',
            ]);

            $existingComment = Comment::where('user_id', auth()->id())
                ->where('book_id', $bookId)
                ->whereHas('moderation', function ($query) {
                    $query->where('status', CommentModeration::STATUS_PENDING);
                })
                ->first();

            if ($existingComment) {
                return response()->json([
                    'message' => 'Já existe um comentário pendente para este livro.'
                ], 400);
            }

            DB::beginTransaction();

            $comment = Comment::create([
                'user_id' => auth()->id(),
                'book_id' => $bookId,
                'comment_text' => $request->comment_text,
                'rating' => $request->rating,
            ]);

            $comment->moderation()->create([
                'user_id' => auth()->id(),
                'status' => CommentModeration::STATUS_PENDING,
                'moderation_date' => now(),
                'notes' => 'Comentário a aguardar aprovação',
            ]);

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

            return response()->json([
                'message' => 'Comentário e avaliação submetidos com sucesso.'
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Erro ao submeter comentário.'
            ], 500);
        }
    }
}
