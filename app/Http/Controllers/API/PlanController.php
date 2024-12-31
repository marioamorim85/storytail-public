<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\User;
use App\Models\Subscription;

/**
 * @OA\Tag(
 *     name="Plans",
 *     description="APIs relacionadas com planos de subscrição"
 * )
 */
class PlanController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/plans",
     *     summary="Lista de planos",
     *     description="Retorna os planos disponíveis e estatísticas de utilizadores",
     *     tags={"Plans"},
     *     @OA\Response(
     *         response=200,
     *         description="Lista de planos com estatísticas",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="plans",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Free"),
     *                     @OA\Property(property="active_users_count", type="integer", example=150),
     *                     @OA\Property(
     *                         property="features",
     *                         type="array",
     *                         @OA\Items(
     *                             type="string",
     *                             example="Acesso a livros gratuitos"
     *                         )
     *                     )
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function getPlans()
    {
        try {
            // Obter estatísticas dos planos
            $plans = [
                [
                    'id' => Plan::FREE,
                    'name' => 'Free',
                    'active_users_count' => Subscription::where('plan_id', Plan::FREE)
                        ->where('status', 'active')
                        ->count(),
                    'features' => [
                        'Acesso a livros gratuitos',
                        'Comentários básicos',
                        'Marcadores de progresso'
                    ]
                ],
                [
                    'id' => Plan::PREMIUM,
                    'name' => 'Premium',
                    'active_users_count' => Subscription::where('plan_id', Plan::PREMIUM)
                        ->where('status', 'active')
                        ->count(),
                    'features' => [
                        'Acesso a todos os livros',
                        'Comentários avançados',
                        'Marcadores de progresso',
                        'Conteúdo exclusivo',
                        'Sem anúncios'
                    ]
                ]
            ];

            return response()->json([
                'plans' => $plans
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao obter informações dos planos'
            ], 500);
        }
    }
}
