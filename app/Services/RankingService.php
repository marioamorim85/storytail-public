<?php

namespace App\Services;

use App\Models\User;
use App\Models\PointAction;
use App\Models\UserPoint;
use App\Models\UserRanking;
use Illuminate\Support\Facades\DB;

class RankingService
{
    /**
     * Adiciona pontos para um utilizador baseado numa ação
     */
    public function addPoints(User $user, string $actionName, $referenceId = null, string $referenceType = null)
    {
        return DB::transaction(function() use ($user, $actionName, $referenceId, $referenceType) {
            // Encontra a ação e seus pontos correspondentes
            $action = PointAction::where('action_name', $actionName)
                ->where('is_active', true)
                ->firstOrFail();

            // Regista os pontos ganhos
            $userPoints = UserPoint::create([
                'user_id' => $user->id,
                'point_action_id' => $action->id,
                'points_earned' => $action->points,
                'reference_id' => $referenceId,
                'reference_type' => $referenceType
            ]);

            // Busca ou cria o ranking do utilizador
            $ranking = UserRanking::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'total_points' => 0,
                    'current_rank' => null,
                    'previous_rank' => null,
                    'rank_level' => 'Bronze',
                    'last_calculated_at' => now()
                ]
            );

            // Atualiza o total de pontos
            $ranking->total_points += $action->points;
            $ranking->last_calculated_at = now();

            // Atualiza o nível baseado nos pontos totais
            $this->updateRankLevel($ranking);

            $ranking->save();

            // Atualiza as posições no ranking
            $this->updateRankPositions();

            return $userPoints;
        });
    }

    /**
     * Atualiza o nível baseado nos pontos totais
     */
    private function updateRankLevel(UserRanking $ranking)
    {
        $ranking->rank_level = match(true) {
            $ranking->total_points >= 1000 => 'Gold',
            $ranking->total_points >= 500 => 'Silver',
            default => 'Bronze'
        };
    }

    /**
     * Atualiza as posições no ranking geral
     */
    private function updateRankPositions()
    {
        $rankings = UserRanking::orderBy('total_points', 'desc')->get();

        foreach ($rankings as $index => $ranking) {
            if ($ranking->current_rank != ($index + 1)) {
                $ranking->previous_rank = $ranking->current_rank;
                $ranking->current_rank = $index + 1;
                $ranking->save();
            }
        }
    }

    /**
     * Obtém o ranking de um utilizador
     */
    public function getUserRanking(User $user)
    {
        return UserRanking::where('user_id', $user->id)->first();
    }

    /**
     * Obtém o histórico de pontos de um utilizador
     */
    public function getUserPointsHistory(User $user, $limit = 10)
    {
        return UserPoint::with('pointAction')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Obtém o top ranking de utilizadores
     */
    public function getTopRanking($limit = 10)
    {
        return UserRanking::with('user')
            ->orderBy('total_points', 'desc')
            ->limit($limit)
            ->get();
    }
}
