<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\User;

/**
 * @OA\Tag(
 *     name="Users",
 *     description="APIs relacionadas com utilizadores e suas recomendações"
 * )
 */
class UserController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/users/{userId}/suggested-books",
     *     summary="Livros sugeridos para o utilizador",
     *     description="Retorna uma lista de livros sugeridos baseados nas tags dos livros lidos e favoritos do utilizador",
     *     tags={"Users"},
     *     @OA\Parameter(
     *         name="userId",
     *         in="path",
     *         required=true,
     *         description="ID do utilizador",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de livros sugeridos",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="Livro Sugerido"),
     *                 @OA\Property(property="description", type="string"),
     *                 @OA\Property(property="read_time", type="integer", example=15),
     *                 @OA\Property(property="access_level", type="integer", example=1),
     *                 @OA\Property(property="is_active", type="boolean", example=true),
     *                 @OA\Property(
     *                     property="tags",
     *                     type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="integer"),
     *                         @OA\Property(property="name", type="string")
     *                     )
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
    public function suggestedBooks($userId, $asJson = false)
    {
        try {
            // Carrega o utilizador com relações necessárias
            $user = User::with(['booksRead.tags', 'favoriteBooks.tags'])
                ->findOrFail($userId);

            // Obtém todas as tags dos livros lidos e favoritos
            $tags = $user->booksRead->pluck('tags.*.id')
                ->flatten()
                ->unique()
                ->merge(
                    $user->favoriteBooks->pluck('tags.*.id')
                        ->flatten()
                        ->unique()
                );

            // Busca livros sugeridos com suas relações
            $suggestedBooks = Book::with(['tags', 'authors', 'ageGroup'])
                ->whereHas('tags', function ($query) use ($tags) {
                    $query->whereIn('tags.id', $tags);
                })
                ->whereNotIn('id', $user->booksRead->pluck('id'))
                ->whereNotIn('id', $user->favoriteBooks->pluck('id'))
                ->where('is_active', true)
                ->get()
                ->map(function ($book) {
                    return [
                        'id' => $book->id,
                        'title' => $book->title,
                        'description' => $book->description,
                        'read_time' => $book->read_time,
                        'access_level' => $book->access_level,
                        'is_active' => $book->is_active,
                        'cover_url' => $book->cover_url,
                        'tags' => $book->tags,
                        'authors' => $book->authors,
                        'age_group' => $book->ageGroup
                    ];
                });

            if ($asJson || request()->wantsJson()) {
                return response()->json($suggestedBooks);
            }

            return $suggestedBooks;

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Utilizador não encontrado'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao processar pedido'], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api_views/users/{userId}/profile",
     *     summary="Perfil do utilizador",
     *     description="Retorna a view do perfil do utilizador com livros lidos, favoritos e sugeridos",
     *     tags={"Users"},
     *     @OA\Parameter(
     *         name="userId",
     *         in="path",
     *         required=true,
     *         description="ID do utilizador",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="View do perfil do utilizador",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Utilizador não encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Erro ao carregar perfil do utilizador")
     *         )
     *     )
     * )
     */
    public function profile($userId)
    {
        try {
            $user = User::findOrFail($userId);

            $booksRead = $user->booksRead()
                ->with(['authors', 'ageGroup', 'tags'])
                ->get();

            $favoriteBooks = $user->favoriteBooks()
                ->with(['authors', 'ageGroup', 'tags'])
                ->get();

            $suggestedBooks = $this->suggestedBooks($userId);

            return view('api_views.partials.profile', compact(
                'user',
                'booksRead',
                'favoriteBooks',
                'suggestedBooks'
            ));

        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao carregar perfil do utilizador');
        }
    }

    /**
     * @OA\Get(
     *     path="/api_views/users",
     *     summary="Lista de utilizadores",
     *     description="Retorna a view com lista de todos os utilizadores",
     *     tags={"Users"},
     *     @OA\Response(
     *         response=200,
     *         description="View com lista de utilizadores"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro ao carregar lista",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Erro ao carregar lista de utilizadores")
     *         )
     *     )
     * )
     */
    public function index()
    {
        try {
            $users = User::with(['booksRead', 'favoriteBooks'])->get();
            return view('api_views.users', compact('users'));
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao carregar lista de utilizadores');
        }
    }
}
