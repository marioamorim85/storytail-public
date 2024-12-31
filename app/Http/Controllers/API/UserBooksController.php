<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="User Books",
 *     description="APIs relacionadas com os livros dos utilizadores"
 * )
 */
class UserBooksController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/users/{userId}/books",
     *     summary="Lista de livros do utilizador",
     *     description="Retorna os livros lidos e favoritos de um utilizador específico",
     *     tags={"User Books"},
     *     @OA\Parameter(
     *         name="userId",
     *         in="path",
     *         required=true,
     *         description="ID do utilizador",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de livros do utilizador",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="books_read",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="title", type="string", example="O Grande Livro"),
     *                     @OA\Property(property="description", type="string", example="Descrição do livro"),
     *                     @OA\Property(property="read_time", type="integer", example=15),
     *                     @OA\Property(property="access_level", type="integer", example=1),
     *                     @OA\Property(property="is_active", type="boolean", example=true),
     *                     @OA\Property(property="progress", type="integer", example=75),
     *                     @OA\Property(property="read_date", type="string", format="date-time")
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="favorite_books",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=2),
     *                     @OA\Property(property="title", type="string", example="Outro Livro"),
     *                     @OA\Property(property="description", type="string", example="Descrição do livro"),
     *                     @OA\Property(property="is_active", type="boolean", example=true),
     *                     @OA\Property(property="favorited_at", type="string", format="date-time")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Utilizador não encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Utilizador não encontrado")
     *         )
     *     )
     * )
     */
    public function listUserBooks($userId, Request $request)
    {
        try {
            $user = User::findOrFail($userId);

            // Carrega os livros lidos com progresso e data
            $booksRead = $user->booksRead()
                ->with(['ageGroup', 'authors'])
                ->get()
                ->map(function ($book) {
                    return [
                        'id' => $book->id,
                        'title' => $book->title,
                        'description' => $book->description,
                        'read_time' => $book->read_time,
                        'access_level' => $book->access_level,
                        'is_active' => $book->is_active,
                        'progress' => $book->pivot->progress,
                        'read_date' => $book->pivot->read_date,
                        'age_group' => $book->ageGroup,
                        'authors' => $book->authors
                    ];
                });

            // Carrega os livros favoritos com data
            $favoriteBooks = $user->favoriteBooks()
                ->with(['ageGroup', 'authors'])
                ->get()
                ->map(function ($book) {
                    return [
                        'id' => $book->id,
                        'title' => $book->title,
                        'description' => $book->description,
                        'is_active' => $book->is_active,
                        'favorited_at' => $book->pivot->created_at,
                        'age_group' => $book->ageGroup,
                        'authors' => $book->authors
                    ];
                });

            return response()->json([
                'books_read' => $booksRead,
                'favorite_books' => $favoriteBooks
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Utilizador não encontrado'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao processar pedido'], 500);
        }
    }
}
