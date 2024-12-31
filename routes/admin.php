<?php

use App\Http\Controllers\Admin\AdminController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\BookController as AdminBookController;
use App\Http\Controllers\Admin\ActivityController as AdminActivityController;
use App\Http\Controllers\Admin\AuthorController as AdminAuthorController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\TagController as AdminTagController;
use App\Http\Controllers\Admin\AgeGroupController as AdminAgeGroupController;
use App\Http\Controllers\Admin\PlanController as AdminPlanController;
use App\Http\Controllers\Admin\SubscriptionController as AdminSubscriptionController;
use App\Http\Controllers\Admin\UserTypeController as AdminUserTypeController;
use App\Http\Controllers\Admin\CommentController as AdminCommentController;
use App\Http\Controllers\Admin\ApprovalController as AdminApprovalController;

// routes/admin.php

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    // Dashboard
    Route::get('/', [AdminController::class, 'index'])->name('dashboard');

    // Books
    Route::prefix('books')->name('books.')->group(function () {
        Route::get('/', [AdminBookController::class, 'index'])->name('list');
        Route::get('/show/{book}', [AdminBookController::class, 'show'])->name('show');
        Route::get('/create', [AdminBookController::class, 'create'])->name('create');
        Route::post('/', [AdminBookController::class, 'store'])->name('store');
        Route::get('/{book}/edit', [AdminBookController::class, 'edit'])->name('edit');
        Route::put('/{book}', [AdminBookController::class, 'update'])->name('update');
        Route::delete('/{book}', [AdminBookController::class, 'destroy'])->name('delete');
        Route::delete('/{book}/page/{page}', [AdminBookController::class, 'removePage'])->name('admin.books.removePage');


        // Tags
        Route::prefix('tags')->name('tags.')->group(function () {
            Route::get('/', [AdminTagController::class, 'index'])->name('list');
            Route::get('/create', [AdminTagController::class, 'create'])->name('create');
            Route::post('/', [AdminTagController::class, 'store'])->name('store');
            Route::get('/{tag}/edit', [AdminTagController::class, 'edit'])->name('edit');
            Route::put('/{tag}', [AdminTagController::class, 'update'])->name('update');
            Route::delete('/{tag}', [AdminTagController::class, 'destroy'])->name('delete');
        });
        // Age Groups
        Route::prefix('age-groups')->name('age-groups.')->group(function () {
            Route::get('/', [AdminAgeGroupController::class, 'index'])->name('list');
            Route::get('/create', [AdminAgeGroupController::class, 'create'])->name('create');
            Route::post('/', [AdminAgeGroupController::class, 'store'])->name('store');
            Route::get('/{ageGroup}/edit', [AdminAgeGroupController::class, 'edit'])->name('edit');
            Route::put('/{ageGroup}', [AdminAgeGroupController::class, 'update'])->name('update');
            Route::delete('/{ageGroup}', [AdminAgeGroupController::class, 'destroy'])->name('delete');
        });
    });

    // Activities
    Route::prefix('activities')->name('activities.')->group(function () {
        Route::get('/', [AdminActivityController::class, 'index'])->name('list');
        Route::get('/show/{activity}', [AdminActivityController::class, 'show'])->name('show');
        Route::get('/create', [AdminActivityController::class, 'create'])->name('create');
        Route::post('/', [AdminActivityController::class, 'store'])->name('store');
        Route::get('/{activity}/edit', [AdminActivityController::class, 'edit'])->name('edit');
        Route::put('/{activity}', [AdminActivityController::class, 'update'])->name('update');
        Route::delete('/{activity}', [AdminActivityController::class, 'destroy'])->name('delete');
        Route::delete('{activity}/image/{image}', [AdminActivityController::class, 'removeImage'])
            ->name('removeImage');
    });

    // Authors
    Route::prefix('authors')->name('authors.')->group(function () {
        Route::get('/', [AdminAuthorController::class, 'index'])->name('list');
        Route::get('/create', [AdminAuthorController::class, 'create'])->name('create');
        Route::post('/', [AdminAuthorController::class, 'store'])->name('store');
        Route::get('/{author}', [AdminAuthorController::class, 'show'])->name('show');
        Route::get('/{author}/edit', [AdminAuthorController::class, 'edit'])->name('edit');
        Route::put('/{author}', [AdminAuthorController::class, 'update'])->name('update');
        Route::delete('/{author}', [AdminAuthorController::class, 'destroy'])->name('delete');
        Route::delete('/{author}/photo', [AdminAuthorController::class, 'removePhoto'])
            ->name('removePhoto');
    });


    // Users
    Route::prefix('users')->name('users.')->group(function () {
        // Rotas sem parâmetros primeiro
        Route::get('/', [AdminUserController::class, 'index'])->name('list');
        Route::get('/create', [AdminUserController::class, 'create'])->name('create');
        Route::post('/', [AdminUserController::class, 'store'])->name('store');

        // Plans
        Route::prefix('plans')->name('plans.')->group(function () {
            Route::get('/', [AdminPlanController::class, 'index'])->name('list');
            Route::get('/create', [AdminPlanController::class, 'create'])->name('create');
            Route::post('/', [AdminPlanController::class, 'store'])->name('store');
            Route::get('/{plan}/edit', [AdminPlanController::class, 'edit'])->name('edit');
            Route::put('/{plan}', [AdminPlanController::class, 'update'])->name('update');
            Route::delete('/{plan}', [AdminPlanController::class, 'destroy'])->name('delete');
        });

        // Subscriptions
        Route::prefix('subscriptions')->name('subscriptions.')->group(function () {
            Route::get('/', [AdminSubscriptionController::class, 'index'])->name('list');
            Route::get('/create', [AdminSubscriptionController::class, 'create'])->name('create');
            Route::post('/', [AdminSubscriptionController::class, 'store'])->name('store');
            Route::get('/{subscription}', [AdminSubscriptionController::class, 'show'])->name('show'); // Adicionar rota show
            Route::get('/{subscriptions}/edit', [AdminSubscriptionController::class, 'edit'])->name('edit');
            Route::put('/{subscriptions}', [AdminSubscriptionController::class, 'update'])->name('update');
            Route::delete('/{subscriptions}', [AdminSubscriptionController::class, 'destroy'])->name('delete');
            Route::post('/{subscription}/moderate', [AdminSubscriptionController::class, 'moderateSubscription'])->name('moderate');
        });


        // User Types
        Route::prefix('user-types')->name('user-types.')->group(function () {
            Route::get('/', [AdminUserTypeController::class, 'index'])->name('list');
            Route::get('/create', [AdminUserTypeController::class, 'create'])->name('create');
            Route::post('/', [AdminUserTypeController::class, 'store'])->name('store');
            Route::get('/{userType}/edit', [AdminUserTypeController::class, 'edit'])->name('edit');
            Route::put('/{userType}', [AdminUserTypeController::class, 'update'])->name('update');
            Route::delete('/{userType}', [AdminUserTypeController::class, 'destroy'])->name('delete');
        });

        // Comments
        Route::prefix('comments')->name('comments.')->group(function () {
            Route::get('/', [AdminCommentController::class, 'index'])->name('list');
            Route::get('/{comment}', [AdminCommentController::class, 'show'])->name('show');
            Route::get('/{comment}/edit', [AdminCommentController::class, 'edit'])->name('edit');
            Route::put('/{comment}', [AdminCommentController::class, 'update'])->name('update');
            Route::delete('/{comment}', [AdminCommentController::class, 'destroy'])->name('delete');
            Route::post('/{comment}/moderate', [AdminCommentController::class, 'moderate'])->name('moderate');
        });

        // Rotas com parâmetros por último
        Route::get('/{user}', [AdminUserController::class, 'show'])->name('show');
        Route::get('/{user}/edit', [AdminUserController::class, 'edit'])->name('edit');
        Route::put('/{user}', [AdminUserController::class, 'update'])->name('update');
        Route::delete('/{user}', [AdminUserController::class, 'destroy'])->name('delete');
        Route::delete('/{user}/photo', [AdminUserController::class, 'removePhoto'])
            ->name('removePhoto');
    });

    Route::prefix('approvals')->name('approvals.')->group(function () {
        Route::get('/comments', [AdminApprovalController::class, 'comments'])->name('comments');
        Route::post('/comments/{id}', [AdminApprovalController::class, 'updateComment'])->name('comments.update');
        Route::get('/subscriptions', [AdminApprovalController::class, 'subscriptions'])->name('subscriptions');
        Route::post('/subscriptions/{id}', [AdminApprovalController::class, 'updateSubscription'])->name('subscriptions.update');
        Route::get('/history', [AdminApprovalController::class, 'history'])->name('history');
    });


    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [AdminReportController::class, 'index'])->name('index');
    });



});



