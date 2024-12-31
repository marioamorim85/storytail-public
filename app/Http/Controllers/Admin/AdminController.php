<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Author;
use App\Models\Book;
use App\Models\CommentModeration;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Tag;
use App\Models\User;
use App\Models\UserType;

class AdminController extends Controller
{
    public function index()
    {
        return view('admin.admin-dashboard', [
            'totalUsers' => $this->getTotalUsers(),
            'totalBooks' => $this->getTotalBooks(),
            'totalActivities' => $this->getTotalActivities(),
            'totalTags' => $this->getTotalTags(),
            'totalComments' => $this->getTotalComments(),
            'newUsers' => $this->getNewUsers(),
            'popularBooks' => $this->getPopularBooks(),
            'mostClickedBooks' => $this->getMostClickedBooks(),
            'pendingComments' => $this->getPendingComments(),
            'pendingSubscriptions' => $this->getPendingSubscriptions(),
            'popularTags' => $this->getPopularTags(),
            'activeSubscriptions' => $this->getActiveSubscriptions(),
            'topPlans' => $this->getTopPlans(),
            'popularAuthors' => $this->getPopularAuthors(),
        ]);
    }

    private function getTotalUsers()
    {
        return User::where('user_type_id', '!=', UserType::ADMIN)->count();
    }


    private function getTotalBooks()
    {
        return Book::count();
    }

    private function getTotalActivities()
    {
        return Activity::count();
    }

    private function getTotalTags()
    {
        return Tag::count();
    }

    private function getTotalComments()
    {
        return CommentModeration::count(); // Usar a tabela de moderação
    }

    private function getNewUsers()
    {
        return User::orderBy('created_at', 'desc')->take(5)->get();
    }

    private function getPopularBooks()
    {
        return Book::withCount('favorites')->orderBy('favorites_count', 'desc')->take(5)->get();
    }

    private function getMostClickedBooks()
    {
        return Book::withCount('clicks')->orderByDesc('clicks_count')->take(5)->get();
    }

    private function getPendingComments()
    {
        return CommentModeration::where('status', 'pending')->count(); // Corrigido para usar a tabela certa
    }

    private function getActiveSubscriptions()
    {
        return Subscription::whereHas('approvals', function($query) {
            $query->where('status', 'approved');
        })->whereHas('plan', function($query) {
            $query->where('name', 'premium'); // Filtra apenas planos premium
        })->count();
    }


    private function getPendingSubscriptions()
    {
        return Subscription::whereHas('approvals', function ($query) {
            $query->where('status', 'pending');
        })->count();
    }

    private function getPopularTags()
    {
        return Tag::withCount('books')->orderByDesc('books_count')->take(5)->get();
    }

    private function getTopPlans()
    {
        return Plan::withCount('subscriptions')->orderByDesc('subscriptions_count')->take(5)->get();
    }

    private function getPopularAuthors()
    {
        return Author::withCount('books')
            ->orderByDesc('books_count')
            ->take(5)
            ->get();
    }
}
