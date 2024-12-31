<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AgeGroup;

/**
 * @OA\Tag(
 *     name="Age Groups",
 *     description="APIs relacionadas com grupos etários e seus livros"
 * )
 */
class AgeGroupController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/age-groups/{id}/books",
     *     summary="Livros por grupo etário",
     *     description="Retorna os livros de um grupo etário específico",
     *     tags={"Age Groups"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do grupo etário",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de livros do grupo etário",
     *         @OA\JsonContent(
     *             @OA\Property(property="age_group", type="string", example="3-5 anos"),
     *             @OA\Property(
     *                 property="books",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="title", type="string", example="O Grande Livro"),
     *                     @OA\Property(property="description", type="string"),
     *                     @OA\Property(property="read_time", type="integer", example=15),
     *                     @OA\Property(property="is_active", type="boolean", example=true),
     *                     @OA\Property(property="cover_url", type="string"),
     *                     @OA\Property(
     *                         property="authors",
     *                         type="array",
     *                         @OA\Items(
     *                             @OA\Property(property="id", type="integer"),
     *                             @OA\Property(property="name", type="string")
     *                         )
     *                     ),
     *                     @OA\Property(
     *                         property="tags",
     *                         type="array",
     *                         @OA\Items(
     *                             @OA\Property(property="id", type="integer"),
     *                             @OA\Property(property="name", type="string")
     *                         )
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Grupo etário não encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Grupo etário não encontrado")
     *         )
     *     )
     * )
     */
    public function getAgeGroupBooks($id)
    {
        try {
            $ageGroup = AgeGroup::with([
                'books' => function($query) {
                    $query->where('is_active', true)
                        ->with(['authors', 'tags']);
                }
            ])->findOrFail($id);

            return response()->json([
                'age_group' => $ageGroup->age_group,
                'books' => $ageGroup->books->map(function($book) {
                    return [
                        'id' => $book->id,
                        'title' => $book->title,
                        'description' => $book->description,
                        'read_time' => $book->read_time,
                        'is_active' => $book->is_active,
                        'cover_url' => $book->cover_url,
                        'authors' => $book->authors->map(function($author) {
                            return [
                                'id' => $author->id,
                                'name' => $author->name
                            ];
                        }),
                        'tags' => $book->tags->map(function($tag) {
                            return [
                                'id' => $tag->id,
                                'name' => $tag->name
                            ];
                        })
                    ];
                })
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Grupo etário não encontrado'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao processar pedido'], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/age-groups",
     *     summary="Lista grupos etários",
     *     description="Retorna todos os grupos etários disponíveis",
     *     tags={"Age Groups"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de grupos etários",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="age_group", type="string", example="3-5 anos")
     *             )
     *         )
     *     )
     * )
     */
    public function getAgeGroups()
    {
        try {
            $ageGroups = AgeGroup::all();
            return response()->json($ageGroups);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao processar pedido'], 500);
        }
    }
}
