<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Plan;
use App\Models\User;
use App\Models\BookClick;
use App\Models\Book;
use App\Models\Comment;
use App\Models\Subscription;
use App\Models\Tag;
use App\Models\Author;
use App\Models\UserType;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        // Estatísticas de utilização
        $usageStats = [
            'totalUsers' => User::where('user_type_id', '!=', UserType::ADMIN)->count(),
            'activeUsers' => User::where('status', 'active')
                ->where('user_type_id', '!=', UserType::ADMIN)
                ->count(),
            'newUsersLastMonth' => User::where('created_at', '>=', now()->subMonth())
                ->where('user_type_id', '!=', UserType::ADMIN)
                ->count(),
        ];


        // Estatísticas de Subscrições
        $subscriptionStats = Plan::withCount([
            'subscriptions as total_subscriptions_count', // Contagem total de subscrições
            'subscriptions as active_subscriptions_count' => function ($query) {
                $query->where('status', 'active') // Apenas subscrições ativas
                ->where('start_date', '<=', now()) // Subscrições com data de início passada ou igual a hoje
                ->where(function ($query) {
                    $query->whereNull('end_date') // Subscrições sem data de término
                    ->orWhere('end_date', '>=', now()); // Ou subscrições que ainda não terminaram
                })
                    ->whereHas('user', function ($userQuery) {
                        $userQuery->where('status', 'active') // Apenas utilizadores ativos
                        ->where('user_type_id', '!=', UserType::ADMIN); // Excluindo administradores
                    });
            },
        ])->get();



        // Livros Populares (últimos 3 meses)
        $popularBooks = BookClick::with('book')
            ->whereHas('book', function ($query) {
                $query->where('is_active', true); // Apenas livros ativos
            })
            ->select('book_id', DB::raw('COUNT(*) as clicks_count'))
            ->where('clicked_at', '>=', now()->subMonths(3))
            ->groupBy('book_id')
            ->orderByDesc('clicks_count')
            ->limit(3)
            ->get();

        // Horários de Pico de Uso
        $peakUsageTimes = DB::table('book_clicks')
            ->select(DB::raw('HOUR(clicked_at) as hour'), DB::raw('COUNT(*) as clicks_count'))
            ->groupBy('hour')
            ->orderByDesc('clicks_count')
            ->limit(3) // Top 3 horários
            ->get();

        /// Atividades Mais Populares
        $mostPopularActivities = DB::table('activities')
            ->select('id', 'title', DB::raw('COUNT(*) as participation_count'))
            ->groupBy('id', 'title')
            ->orderByDesc('participation_count')
            ->take(5)
            ->get();

        // Autores Mais Populares
        $popularAuthors = $this->getPopularAuthors();

        // Tags Populares e Total de Tags
        $popularTags = $this->getPopularTags();
        $totalTags = $this->getTotalTags();

        // Livros Populares por Favoritos
        $popularBooksByFavorites = $this->getPopularBooks();

        // Top 5 utilizadores por ranking
        $topUsers = $this->getTopUsers();

        return view('admin.reports.index', compact(
            'usageStats',
            'subscriptionStats',
            'popularBooks',
            'peakUsageTimes',
            'mostPopularActivities',
            'popularAuthors',
            'popularTags',
            'totalTags',
            'popularBooksByFavorites',
            'topUsers'
        ));
    }

    private function getPopularBooks()
    {
        return Book::withCount('favorites')
            ->where('is_active', true)
            ->orderBy('favorites_count', 'desc')
            ->take(5)
            ->get();
    }

    private function getTotalTags()
    {
        return Tag::count();
    }

    private function getPopularAuthors()
    {
        return Author::withCount('books')
            ->whereHas('books', function ($query) {
                $query->where('is_active', true);
            })
            ->orderByDesc('books_count')
            ->take(5)
            ->get();
    }

    private function getPopularTags()
    {
        return Tag::withCount('books')
            ->orderByDesc('books_count')
            ->take(5)
            ->get();
    }

    private function getTopUsers()
    {
        return User::with(['ranking', 'points'])
            ->whereHas('ranking')
            ->where('status', 'active')
            ->where('user_type_id', '!=', UserType::ADMIN)
            ->select('users.*')
            ->leftJoin('user_rankings', 'users.id', '=', 'user_rankings.user_id')
            // Garantir ordenação pelos pontos e pelo current_rank
            ->orderBy('user_rankings.total_points', 'desc')
            ->take(5)
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->getFullName(),
                    'current_rank' => $user->getCurrentRank(),
                    'total_points' => $user->getTotalPoints(),
                    'last_updated' => $user->ranking->last_calculated_at->diffForHumans()
                ];
            })
            ->sortBy('current_rank')
            ->values();
    }
}
