<?php

use App\Http\Controllers\API\AuthorController;
use App\Http\Controllers\API\CommentController;
use App\Http\Controllers\API\PlanController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\BookController;
use App\Http\Controllers\API\AgeGroupController;
use App\Http\Controllers\API\UserBooksController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\AdminController;
use App\Http\Controllers\API\ActivityController;

/**
 * @OA\Info(
 *     title="Storytail API",
 *     version="1.0.0",
 *     description="API documentation for the Storytail project"
 * )
 */

// Rotas para views da API
Route::prefix('api_views')->group(function () {
    // View principal
    Route::get('/', function () {
        return view('api_views.index');
    });

    // Views de livros
    Route::get('/books', function () {
        return view('api_views.books');
    });

    // Views de administração
    Route::get('/admin', function () {
        return view('api_views.admin');
    });

    // Views de utilizadores
    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::get('/{userId}/profile', [UserController::class, 'profile']);
    });
});

// Rotas da API JSON
Route::prefix('api')->group(function () {
    // Rotas de livros
    Route::prefix('books')->group(function () {
        Route::get('/', [BookController::class, 'listBooks'])->name('books.json');
        Route::get('/{id}', [BookController::class, 'show']);
        Route::get('/{bookId}/activities', [ActivityController::class, 'listActivitiesByBook'])->name('books.activities');
    });

    // Rotas de autores
    Route::get('authors/{id}', [AuthorController::class, 'show'])->name('api.authors.show');

    // Rotas de utilizadores
    Route::prefix('users')->group(function () {
        Route::get('{userId}/books', [UserBooksController::class, 'listUserBooks']);
        Route::get('{userId}/suggested-books', [UserController::class, 'suggestedBooks']);
    });

    // Rotas de administração
    Route::prefix('admin')->group(function () {
        Route::get('popular-books', [AdminController::class, 'popularBooks'])->name('admin.popular-books.json');
        Route::get('peak-usage-times', [AdminController::class, 'peakUsageTimes'])->name('admin.peak-usage-times.json');
    });

    // Grupos Etários
    Route::prefix('age-groups')->group(function () {
        Route::get('/', [AgeGroupController::class, 'getAgeGroups'])->name('age-groups.list');
        Route::get('/{id}/books', [AgeGroupController::class, 'getAgeGroupBooks'])->name('age-groups.books');
    });

    // Rotas de comentários
    Route::prefix('books/{bookId}/comments')->group(function () {
        Route::get('/', [CommentController::class, 'getBookComments']);
        Route::post('/', [CommentController::class, 'store'])->middleware('auth');
    });

    // Rotas de planos
    Route::get('plans', [PlanController::class, 'getPlans']);
});

// Rotas auxiliares para filtros e dados
Route::get('/books/filter', [BookController::class, 'filter'])->name('books.filter');
Route::get('/age-groups', [AgeGroupController::class, 'getAgeGroups'])->name('age_groups');
Route::get('/tags', function () {
    return App\Models\Tag::all();
})->name('tags');

// Rotas de estatísticas admin
Route::prefix('admin')->group(function () {
    Route::get('popular-books', [AdminController::class, 'popularBooks']);
    Route::get('peak-usage-times', [AdminController::class, 'peakUsageTimes']);
});
