<?php

use App\Http\Controllers\Front\AuthorController as FrontAuthorController;
use App\Http\Controllers\Front\BookController as FrontBookController;
use App\Http\Controllers\Front\CommentController as FrontCommentController;
use App\Http\Controllers\Front\ContactController as ContactController;
use App\Http\Controllers\Front\ProfileController as FrontProfileController;
use App\Http\Controllers\Front\PlanController as FrontPlanController;
use Illuminate\Support\Facades\Route;

// Rota principal
Route::get('/', [FrontBookController::class, 'index'])->name('home');

// Rotas protegidas
Route::middleware(['auth', 'verified'])->group(function () {
    // Rota para perfil
    Route::get('/profile', function () {
        return view('manage-acount.profile');
    })->name('profile');

    // Rotas do Breeze para gestão de perfil
    Route::get('/profile/edit', [FrontProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [FrontProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile', [FrontProfileController::class, 'showProfilePage'])->name('profile');
    Route::patch('/profile/remove-photo', [FrontProfileController::class, 'removePhoto'])->name('profile.removePhoto');
    Route::delete('/profile', [FrontProfileController::class, 'destroy'])->name('profile.destroy');
    Route::patch('/profile/plan/downgrade', [FrontPlanController::class, 'downgrade'])->name('profile.downgrade');
    Route::post('/profile/plan/request-premium', [FrontPlanController::class, 'requestPremium'])->name('profile.requestPremium');



    // Rota para adicionar comentários
    Route::post('/book/{book}/comment', [FrontCommentController::class, 'store'])->name('book.comment');

    // Rota para favoritar livros
    Route::post('/book-details/{id}/favorite', [FrontBookController::class, 'toggleFavorite'])
        ->name('book.toggle-favorite');


    // Rota para guardar progresso de leitura
    Route::post('/book-details/{id}/progress', [FrontBookController::class, 'saveProgress'])
        ->name('book.saveProgress');

    // Rota para atualizar progresso de atividades
    Route::post('/activities/{activity}/progress', [FrontBookController::class, 'updateActivityProgress'])
        ->name('activity.progress')
        ->middleware('auth');

    // Rota para verificar progresso de atividades
    Route::get('/activities/{activityId}/check-progress', [FrontBookController::class, 'checkActivityProgress'])
        ->name('activity.check-progress')
        ->middleware('auth');

    // Rota para a página de favoritos
    Route::get('/manage-my-books/favourites', [FrontBookController::class, 'myFavourites'])
        ->middleware('auth')
        ->name('favourites');

    // Rota para a página de progresso dos livros
    Route::get('/manage-my-books/progress', [FrontBookController::class, 'myBooksProgress'])
        ->middleware('auth')
        ->name('my-books-progress');

    // Rota para a página de index dos badges (menu principal)
    Route::get('/manage-my-books/index', [FrontBookController::class, 'badgesIndex'])
        ->middleware('auth')
        ->name('badges-index');


    // Rota para a página dos badges dos livros
    Route::get('/manage-my-books/badges', [FrontBookController::class, 'bookBadges'])
        ->middleware('auth')
        ->name('book-badges');

    // Rota para a página dos badges das atividades
    Route::get('/manage-my-books/activity-badges', [FrontBookController::class, 'activityBadges'])
        ->middleware('auth')
        ->name('activity-badges');
});





// Rota para book details
Route::get('/book-details/{id}', [FrontBookController::class, 'show'])->name('book-index');

// Rota para o click de um livro
Route::post('/books/{id}/click', [FrontBookController::class, 'registerClick'])
    ->name('book.click');

// Rota para o autor de um livro
Route::get('/book-details/author/{id}', [FrontAuthorController::class, 'show'])
    ->name('author.show');

// Rota para pesquisa de livros
Route::get('/books/search', [FrontBookController::class, 'search'])->name('books.search');

// Rota para filtrar livros no asc e desc
Route::get('/books/sort', [FrontBookController::class, 'sortBooks'])->name('books.sort');

// Rota para obter opções de filtros
Route::get('/books/filter-options', [FrontBookController::class, 'getFilterOptions'])->name('books.filterOptions');

// Rota para aplicar filtros nos livros
Route::get('/books/apply-filters', [FrontBookController::class, 'filterBooks'])->name('books.applyFilters');

// Rotas para contactos
Route::get('/contacts', function () {
    return view('utility-pages.contact');
})->name('contacts');

Route::post('/contacts', [ContactController::class, 'store'])->name('contacts.store');

// Rota para about
Route::get('/about', function () {
    return view('utility-pages.about');
})->name('about');


// Rotas admin protegidas
require __DIR__ . '/admin.php';

// Rotas de autenticação do Breeze
require __DIR__ . '/auth.php';

// Rotas API
require __DIR__ . '/api.php';
