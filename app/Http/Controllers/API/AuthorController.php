<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Author;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Authors",
 *     description="APIs relacionadas a autores"
 * )
 */
class AuthorController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/authors/{id}",
     *     summary="Detalhes do autor com livros ativos",
     *     description="Retorna os detalhes de um autor e os seus livros ativos.",
     *     tags={"Authors"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do autor",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Parameter(
     *         name="book_id",
     *         in="query",
     *         required=false,
     *         description="ID do livro a excluir da listagem",
     *         @OA\Schema(type="integer", example=5)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalhes do autor com livros ativos.",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="description", type="string", example="Renowned international author."),
     *             @OA\Property(property="nationality", type="string", example="American"),
     *             @OA\Property(property="author_photo_url", type="string", example="photos/john_doe.png"),
     *             @OA\Property(
     *                 property="books",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=2),
     *                     @OA\Property(property="title", type="string", example="The Great Book"),
     *                     @OA\Property(property="description", type="string", example="A detailed description of the book."),
     *                     @OA\Property(property="read_time", type="integer", example=120),
     *                     @OA\Property(property="is_active", type="boolean", example=true),
     *                     @OA\Property(property="cover_url", type="string", example="covers/great_book.png"),
     *                     @OA\Property(
     *                         property="age_group",
     *                         type="object",
     *                         nullable=true,
     *                         @OA\Property(property="id", type="integer", example=3),
     *                         @OA\Property(property="name", type="string", example="3-5 years")
     *                     ),
     *                     @OA\Property(
     *                         property="tags",
     *                         type="array",
     *                         @OA\Items(
     *                             @OA\Property(property="id", type="integer", example=1),
     *                             @OA\Property(property="name", type="string", example="Fantasy")
     *                         )
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=404, description="Autor não encontrado.")
     * )
     */
    public function show($id, Request $request)
    {
        // Carregar o autor e os seus livros ativos, excluindo opcionalmente um livro específico
        $author = Author::with(['books' => function ($query) use ($request) {
            $query->where('is_active', true)
                ->when($request->book_id, function ($q, $bookId) {
                    $q->where('id', '!=', $bookId);
                })
                ->with(['tags', 'ageGroup']); // Carrega os tags e o grupo etário
        }])->findOrFail($id);

        // Responder com os dados formatados
        return response()->json([
            'id' => $author->id,
            'name' => $author->name, // Nome completo obtido do modelo
            'description' => $author->description,
            'nationality' => $author->nationality,
            'author_photo_url' => $author->author_photo_url,
            'books' => $author->books->map(function ($book) {
                return [
                    'id' => $book->id,
                    'title' => $book->title,
                    'description' => $book->description,
                    'read_time' => $book->read_time,
                    'is_active' => $book->is_active,
                    'cover_url' => $book->cover_url,
                    'age_group' => $book->ageGroup ? [
                        'id' => $book->ageGroup->id,
                        'name' => $book->ageGroup->name,
                    ] : null,
                    'tags' => $book->tags->map(function ($tag) {
                        return [
                            'id' => $tag->id,
                            'name' => $tag->name,
                        ];
                    }),
                ];
            }),
        ]);
    }
}
