<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\BookClick;

/**
 * @OA\Tag(
 *     name="Admin",
 *     description="APIs para funcionalidades administrativas"
 * )
 */
class AdminController extends Controller
{
    /**
     * @OA\Get(
     *     path="/admin/popular-books",
     *     summary="Livros mais populares",
     *     description="Retorna os 3 livros mais clicados nos últimos 3 meses",
     *     tags={"Admin"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista dos livros mais populares",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="book_id", type="integer", example=1),
     *                 @OA\Property(property="clicks_count", type="integer", example=150),
     *                 @OA\Property(
     *                     property="book",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="title", type="string", example="O Livro Mais Popular"),
     *                     @OA\Property(property="description", type="string"),
     *                     @OA\Property(property="read_time", type="integer", example=15),
     *                     @OA\Property(property="access_level", type="integer", example=1),
     *                     @OA\Property(property="is_active", type="boolean", example=true),
     *                     @OA\Property(
     *                         property="authors",
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
     *         response=500,
     *         description="Erro ao processar pedido",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Erro ao processar pedido")
     *         )
     *     )
     * )
     */
    public function popularBooks()
    {
        try {
            $threeMonthsAgo = now()->subMonths(3);

            $popularBooks = BookClick::selectRaw('book_id, COUNT(*) as clicks_count')
                ->where('clicked_at', '>=', $threeMonthsAgo)
                ->groupBy('book_id')
                ->orderByDesc('clicks_count')
                ->with(['book' => function($query) {
                    $query->with(['authors', 'ageGroup']); // Eager loading de relações adicionais
                }])
                ->limit(3)
                ->get();

            return response()->json($popularBooks);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao processar pedido'], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/admin/peak-usage-times",
     *     summary="Horários de pico",
     *     description="Retorna o horário com mais cliques em livros",
     *     tags={"Admin"},
     *     @OA\Response(
     *         response=200,
     *         description="Horário com mais acessos",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(
     *                     property="hour",
     *                     type="integer",
     *                     description="Hora do dia (0-23)",
     *                     example=14
     *                 ),
     *                 @OA\Property(
     *                     property="clicks_count",
     *                     type="integer",
     *                     description="Número de cliques nessa hora",
     *                     example=250
     *                 ),
     *                 @OA\Property(
     *                     property="percentage",
     *                     type="number",
     *                     description="Percentagem do total de cliques",
     *                     example=15.5
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Erro ao processar pedido",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Erro ao processar pedido")
     *         )
     *     )
     * )
     */
    public function peakUsageTimes()
    {
        try {
            // Obter total de cliques para calcular percentagem
            $totalClicks = BookClick::count();

            $peakTimes = BookClick::selectRaw('
               HOUR(clicked_at) as hour,
               COUNT(*) as clicks_count,
               (COUNT(*) / ?) * 100 as percentage',
                [$totalClicks]
            )
                ->groupBy('hour')
                ->orderByDesc('clicks_count')
                ->limit(1)
                ->get();

            return response()->json($peakTimes);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao processar pedido'], 500);
        }
    }
}
