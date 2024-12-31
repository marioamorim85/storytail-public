<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Book;

/**
 * @OA\Tag(
 *     name="Activities",
 *     description="APIs relacionadas com atividades dos livros"
 * )
 */
class ActivityController extends Controller
{
    /**
     * @OA\Schema(
     *     schema="Activity",
     *     type="object",
     *     description="Atividade relacionada com um livro",
     *     @OA\Property(
     *         property="id",
     *         type="integer",
     *         description="ID da atividade",
     *         example=1
     *     ),
     *     @OA\Property(
     *         property="title",
     *         type="string",
     *         description="Título da atividade",
     *         example="Atividade de Leitura"
     *     ),
     *     @OA\Property(
     *         property="description",
     *         type="string",
     *         description="Descrição da atividade",
     *         example="Uma atividade relacionada com a leitura do livro"
     *     ),
     *     @OA\Property(
     *         property="is_active",
     *         type="boolean",
     *         description="Estado da atividade",
     *         example=true
     *     )
     * )
     */

    /**
     * @OA\Get(
     *     path="/api/books/{bookId}/activities",
     *     summary="Lista de atividades do livro",
     *     description="Retorna todas as atividades associadas a um livro específico",
     *     tags={"Activities"},
     *     @OA\Parameter(
     *         name="bookId",
     *         in="path",
     *         required=true,
     *         description="ID do livro",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de atividades",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="Atividade de Leitura"),
     *                 @OA\Property(property="description", type="string", example="Descrição da atividade"),
     *                 @OA\Property(property="is_active", type="boolean", example=true),
     *                 @OA\Property(
     *                     property="images",
     *                     type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="integer"),
     *                         @OA\Property(property="title", type="string"),
     *                         @OA\Property(property="image_url", type="string")
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Livro não encontrado",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Livro não encontrado")
     *         )
     *     )
     * )
     */
    public function listActivitiesByBook($bookId)
    {
        try {
            // Carrega o livro com suas atividades e imagens relacionadas
            $book = Book::with(['activities.activityImages'])
                ->findOrFail($bookId);

            // Retorna apenas atividades ativas e formatadas
            $activities = $book->activities->map(function ($activity) {
                return [
                    'id' => $activity->id,
                    'title' => $activity->title,
                    'description' => $activity->description,
                    'is_active' => $activity->is_active,
                    'images' => $activity->activityImages->map(function ($image) {
                        return [
                            'id' => $image->id,
                            'title' => $image->title,
                            'image_url' => $image->image_url
                        ];
                    })
                ];
            });

            return response()->json($activities);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Livro não encontrado'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao processar pedido'], 500);
        }
    }
}
