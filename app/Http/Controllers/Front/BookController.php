<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\AgeGroup;
use App\Models\Author;
use App\Models\BookClick;
use App\Models\CommentModeration;
use App\Models\Tag;
use App\Services\RankingService;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class BookController extends Controller
{
    private $rankingService;

    public function __construct()
    {
        $this->rankingService = new RankingService();
    }

    /**
     * Display home page with books list
     */
    public function index()
    {
        try {
            $books = Book::with(['authors', 'ageGroup', 'activities', 'tags'])
                ->where('is_active', true)
                ->orderBy('title', 'asc')
                ->get();

            $popularBooks = $this->getPopularBooks();
            $recommendedBooks = $this->getRecommendedBooks();

            return view('home', compact('books', 'popularBooks', 'recommendedBooks'));

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'An error occurred while loading the books.');
        }
    }

    /**
     * Display book details
     */
    public function show($id)
    {
        try {
            $book = Book::with([
                'authors',
                'ageGroup',
                'activities' => function($query) {
                    $query->with(['activityImages' => function($q) {
                        $q->orderBy('order', 'asc');
                    }]);
                },
                'tags',
                'pages' => function ($query) {
                    $query->orderBy('page_index', 'asc');
                },
                'video',
                'userFavorite' => function($query) {
                    $query->where('user_id', auth()->id());
                },
                'avgRating',
                'comments' => function($query) {
                    $query->with(['user', 'moderation'])
                        ->whereHas('moderation', function($q) {
                            $q->where('status', CommentModeration::STATUS_APPROVED);
                        })
                        ->orderBy('created_at', 'desc');
                }
            ])
                ->where('is_active', true)
                ->findOrFail($id);

            // Obter progresso do livro para o usuário logado
            $book->userProgress = DB::table('book_user_read')
                ->where('book_id', $id)
                ->where('user_id', auth()->id())
                ->value('progress') ?? 0;

            foreach ($book->activities as $activity) {
                $activityBook = $book->activities()
                    ->where('activity_id', $activity->id)
                    ->first()
                    ->pivot;

                $activity->userProgress = DB::table('activity_book_user')
                    ->where('activity_book_id', $activityBook->id)
                    ->where('user_id', auth()->id())
                    ->value('progress') ?? 0;
            }

            $book->averageRating = $book->avgRating->avg('rating') ?? 0;
            $hasAccess = $this->checkBookAccess($book);

            $relatedBooks = Book::with(['authors', 'ageGroup', 'tags'])
                ->where('is_active', true)
                ->where('id', '!=', $book->id)
                ->where(function ($query) use ($book) {
                    $query->where('age_group_id', $book->age_group_id)
                        ->orWhereHas('tags', function ($tagQuery) use ($book) {
                            $tagQuery->whereIn('tags.id', $book->tags->pluck('id'));
                        });
                })
                ->take(4)
                ->get();

            return view('book-details.book-index', compact('book', 'hasAccess', 'relatedBooks'));

        } catch (\Exception $e) {
            return redirect()
                ->route('home')
                ->with('error', 'An error occurred while loading the book.');
        }
    }

    // Método para obter as opções de filtro
    public function getFilterOptions()
    {
        try {
            $filterOptions = [
                'plans' => [
                    ['id' => 1, 'name' => 'Free'],
                    ['id' => 2, 'name' => 'Premium']
                ],
                'ages' => AgeGroup::select('id', 'age_group as name')->get(),
                'tags' => Tag::select('id', 'name')->orderBy('name')->get(),
                'authors' => Author::all()->map(function($author) {
                    return [
                        'id' => $author->id,
                        'name' => $author->name  // Isso usará o accessor getNameAttribute
                    ];
                }),
                'readingTimes' => [
                    ['id' => 1, 'name' => '<10min.', 'min' => 0, 'max' => 10],
                    ['id' => 2, 'name' => '10min. - 14min.', 'min' => 10, 'max' => 15],
                    ['id' => 3, 'name' => '15min. - 19min.', 'min' => 15, 'max' => 20],
                    ['id' => 4, 'name' => '>=20min.', 'min' => 20]
                ],
            ];

            return response()->json($filterOptions);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to load filter options'], 500);
        }
    }



    // Método para registar um clique num livro
    public function registerClick($bookId)
    {
        try {
            DB::transaction(function() use ($bookId) {
                BookClick::create([
                    'book_id' => $bookId,
                    'clicked_at' => now()->setTimezone('Europe/Lisbon')
                ]);

                if (auth()->check()) {
                    $this->rankingService->addPoints(
                        auth()->user(),
                        'read_book',
                        $bookId,
                        'book'
                    );
                }
            });

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            \Log::error('Error registering click: ' . $e->getMessage());
            return response()->json(['success' => false], 500);
        }
    }


    // Método para obter os livros populares
    public function getPopularBooks()
    {
        return Book::withCount(['clicks' => function($query) {
            $query->where('clicked_at', '>=', now()->subMonths(3));
        }])
            ->withCount('favorites')
            ->where('is_active', true)
            ->orderByDesc('clicks_count')
            ->orderByDesc('favorites_count')
            ->take(4)
            ->get()
            ->each(function ($book) {
                \Log::info("Book: {$book->title}, Clicks: {$book->clicks_count}, Favorites: {$book->favorites_count}");
            });

    }

    // Método para obter os livros recomendados
    public function getRecommendedBooks()
    {
        $utilizador = auth()->user();
        $limite = !$utilizador ? 4 : 8;

        // Para utilizadores não autenticados, devolve 4 livros aleatórios
        if (!$utilizador) {
            return Book::where('is_active', true)
                ->inRandomOrder()
                ->orderBy('title', 'asc')
                ->take(4)
                ->get();
        }

        // Busca as tags dos livros lidos pelo utilizador
        $tagsLivrosLidos = DB::table('book_user_read')
            ->join('books', 'books.id', '=', 'book_user_read.book_id')
            ->join('tagging_tagged', 'books.id', '=', 'tagging_tagged.book_id')
            ->where('book_user_read.user_id', $utilizador->id)
            ->pluck('tagging_tagged.tag_id');

        // Busca as faixas etárias dos livros lidos
        $faixasEtariasLidas = DB::table('book_user_read')
            ->join('books', 'books.id', '=', 'book_user_read.book_id')
            ->where('book_user_read.user_id', $utilizador->id)
            ->pluck('books.age_group_id');

        // Tenta recomendar livros com base nas tags e faixas etárias
        $livrosRecomendados = Book::where('is_active', true)
            ->where(function ($query) use ($tagsLivrosLidos, $faixasEtariasLidas) {
                $query->whereHas('tags', function ($q) use ($tagsLivrosLidos) {
                    $q->whereIn('tags.id', $tagsLivrosLidos);
                })->orWhereIn('age_group_id', $faixasEtariasLidas);
            })
            ->whereNotIn('id', function ($query) use ($utilizador) {
                $query->select('book_id')
                    ->from('book_user_read')
                    ->where('user_id', $utilizador->id);
            })
            ->inRandomOrder()
            ->take($limite)
            ->get();

        // Se não encontrar recomendações suficientes, garante que pelo menos 3 livros sejam devolvidos
        if ($livrosRecomendados->count() < 3) {
            $livrosAdicionais = Book::where('is_active', true)
                ->whereNotIn('id', $livrosRecomendados->pluck('id')->toArray())
                ->inRandomOrder()
                ->take(4 - $livrosRecomendados->count())
                ->get();

            // Combina os livros recomendados com os adicionais
            $livrosRecomendados = $livrosRecomendados->merge($livrosAdicionais);
        }

        return $livrosRecomendados;
    }


    // Método para verificar se o utilizador tem acesso a um livro
    private function checkBookAccess(Book $book)
    {
        // Se o livro for free (access_level = 1), permite acesso
        if ($book->access_level == 1) {
            return true;
        }

        // Se não houver utilizador autenticado, não permite acesso a conteúdo premium
        if (!auth()->check()) {
            return false;
        }

        // Usa o método que já existe no modelo User
        return auth()->user()->canAccessPremiumContent();
    }

    //Método para marcar um livro como favorito
    public function toggleFavorite($id)
    {
        try {
            DB::beginTransaction();

            $book = Book::findOrFail($id);
            $user = auth()->user();

            $isFavorite = $user->favoriteBooks()->where('book_id', $id)->exists();

            if ($isFavorite) {
                $user->favoriteBooks()->detach($id);
                $message = "\"$book->title\" has been removed from favorites!";
                $status = 'removed';
            } else {
                $user->favoriteBooks()->attach($id);
                $message = "\"$book->title\" has been added to favorites!";
                $status = 'added';

                $this->rankingService->addPoints($user, 'add_favorite', $id, 'book');
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $message,
                'status' => $status
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating favorites. Please try again.'
            ], 500);
        }
    }
    public function saveProgress(Request $request, $bookId)
    {
        try {
            $request->validate([
                'progress' => 'required|integer|min:0|max:100'
            ]);

            $user = auth()->user();
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated.'
                ], 401);
            }

            $book = Book::findOrFail($bookId);
            $currentProgress = DB::table('book_user_read')
                ->where('book_id', $bookId)
                ->where('user_id', $user->id)
                ->value('progress') ?? 0;

            // Só atualiza se o progresso for maior
            if ($request->progress > $currentProgress) {
                try {
                    DB::beginTransaction();

                    // Primeiro atualiza o progresso
                    DB::table('book_user_read')
                        ->updateOrInsert(
                            [
                                'book_id' => $bookId,
                                'user_id' => $user->id,
                            ],
                            [
                                'progress' => $request->progress,
                                'read_date' => $request->progress == 100 ? now()->format('Y-m-d H:i:s') : null,
                                'created_at' => now()->format('Y-m-d H:i:s'),
                                'updated_at' => now()->format('Y-m-d H:i:s')
                            ]
                        );

                    // Depois atribui os pontos
                    try {
                        if ($currentProgress < 25 && $request->progress >= 25) {
                            \Log::info('Atribuindo pontos 25%', ['user' => $user->id, 'book' => $bookId]);
                            $this->rankingService->addPoints($user, 'book_progress_25', $bookId, 'book');
                        }
                        if ($currentProgress < 50 && $request->progress >= 50) {
                            \Log::info('Atribuindo pontos 50%', ['user' => $user->id, 'book' => $bookId]);
                            $this->rankingService->addPoints($user, 'book_progress_50', $bookId, 'book');
                        }
                        if ($currentProgress < 75 && $request->progress >= 75) {
                            \Log::info('Atribuindo pontos 75%', ['user' => $user->id, 'book' => $bookId]);
                            $this->rankingService->addPoints($user, 'book_progress_75', $bookId, 'book');
                        }
                        if ($currentProgress < 100 && $request->progress == 100) {
                            \Log::info('Atribuindo pontos 100%', ['user' => $user->id, 'book' => $bookId]);
                            $this->rankingService->addPoints($user, 'book_progress_100', $bookId, 'book');
                        }
                    } catch (\Exception $e) {
                        \Log::error('Erro ao atribuir pontos: ' . $e->getMessage(), [
                            'user' => $user->id,
                            'book' => $bookId,
                            'progress' => $request->progress,
                            'error' => $e->getMessage()
                        ]);
                        throw $e;
                    }

                    DB::commit();

                    return response()->json([
                        'success' => true,
                        'message' => 'Progress saved successfully'
                    ]);
                } catch (\Exception $e) {
                    DB::rollBack();
                    \Log::error('Erro na transação de progresso: ' . $e->getMessage(), [
                        'user' => $user->id,
                        'book' => $bookId,
                        'progress' => $request->progress,
                        'error' => $e->getMessage()
                    ]);
                    throw $e;
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'No update needed'
            ]);

        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }

    public function updateActivityProgress(Request $request, $activityBookId)
    {
        try {
            $request->validate([
                'progress' => 'required|integer|min:0|max:100'
            ]);

            $currentProgress = DB::table('activity_book_user')
                ->where('activity_book_id', $activityBookId)
                ->where('user_id', auth()->id())
                ->value('progress') ?? 0;

            DB::transaction(function() use ($request, $activityBookId, $currentProgress) {
                DB::table('activity_book_user')->updateOrInsert(
                    [
                        'activity_book_id' => $activityBookId,
                        'user_id' => auth()->id()
                    ],
                    [
                        'progress' => $request->progress,
                        'updated_at' => now()->format('Y-m-d H:i:s'),
                        'created_at' => now()->format('Y-m-d H:i:s')
                    ]
                );

                if ($currentProgress < 50 && $request->progress >= 50) {
                    $this->rankingService->addPoints(
                        auth()->user(),
                        'activity_progress_50',
                        $activityBookId,
                        'activity'
                    );
                }

                if ($currentProgress < 100 && $request->progress == 100) {
                    $this->rankingService->addPoints(
                        auth()->user(),
                        'activity_progress_100',
                        $activityBookId,
                        'activity'
                    );

                    $this->rankingService->addPoints(
                        auth()->user(),
                        'complete_activity',
                        $activityBookId,
                        'activity'
                    );
                }
            });

            return response()->json([
                'success' => true,
                'message' => 'Progress updated successfully'
            ]);

        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }

    // Método para ordenar livros
    public function sortBooks(Request $request)
    {
        return $this->filterBooks($request);
    }


    // Método para aplicar filtros, ordenação e pesquisa diretamente
    public function filterBooks(Request $request)
    {
        try {
            // Query base
            $query = Book::select('id', 'title', 'cover_url', 'access_level')
                ->with(['authors:id,first_name,last_name'])
                ->where('is_active', true);

            // Aplica os filtros
            if ($request->filled('plans') && is_array($request->input('plans'))) {
                $query->whereIn('access_level', $request->input('plans'));
            }

            if ($request->filled('ages') && is_array($request->input('ages'))) {
                $query->whereIn('age_group_id', $request->input('ages'));
            }

            if ($request->filled('tags') && is_array($request->input('tags'))) {
                $query->whereHas('tags', function ($q) use ($request) {
                    $q->whereIn('tags.id', $request->input('tags'));
                });
            }

            if ($request->filled('authors') && is_array($request->input('authors'))) {
                $query->whereHas('authors', function ($q) use ($request) {
                    $q->whereIn('authors.id', $request->input('authors'));
                });
            }

            if ($request->filled('readingTimes') && is_array($request->input('readingTimes'))) {
                $query->where(function ($q) use ($request) {
                    foreach ($request->input('readingTimes') as $timeRange) {
                        switch ($timeRange) {
                            case 1:
                                $q->orWhere('read_time', '<=', 9);
                                break;
                            case 2:
                                $q->orWhereBetween('read_time', [10, 14]);
                                break;
                            case 3:
                                $q->orWhereBetween('read_time', [15, 19]);
                                break;
                            case 4:
                                $q->orWhere('read_time', '>=', 20);
                                break;
                        }
                    }
                });
            }

            // Aplica pesquisa
            if ($request->filled('query')) {
                $query->where('title', 'LIKE', "%{$request->input('query')}%");
            }

            // Aplica ordenação
            if ($request->filled('sort')) {
                $query->orderBy('title', $request->input('sort', 'asc'));
            }

            // Obtém os resultados
            $books = $query->get();

            return response()->json(['success' => true, 'books' => $books]);

        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }

    public function checkActivityProgress(Request $request, $activityId)
    {
        try {
            $activityBook = DB::table('activity_book')
                ->where('activity_id', $activityId)
                ->where('book_id', $request->book_id)
                ->first();

            if (!$activityBook) {
                throw new \Exception('Activity Book not found');
            }

            $progress = DB::table('activity_book_user')
                ->where('activity_book_id', $activityBook->id)
                ->where('user_id', auth()->id())
                ->value('progress') ?? 0;

            return response()->json([
                'success' => true,
                'progress' => $progress
            ]);
        } catch (\Throwable $e) {
            return $this->handleException($e);
        }
    }


    // Handler genérico para exceções
    private function handleException(\Throwable $e)
    {
        \Log::error('Erro no controlador de livros:', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);

        return response()->json([
            'success' => false,
            'message' => 'An error occurred. Please try again later.',
            'error' => $e->getMessage(),
        ], 500);
    }

    // Método para obter os favoritos do utilizador
    public function myFavourites()
    {
        $user = auth()->user();

        $favourites = $user->favoriteBooks()
            ->leftJoin('book_user_read', function ($join) use ($user) {
                $join->on('books.id', '=', 'book_user_read.book_id')
                    ->where('book_user_read.user_id', '=', $user->id);
            })
            ->select('books.id', 'books.title', 'books.cover_url', 'books.read_time', 'book_user_read.progress')
            ->get();

        return view('manage-my-books.favourites', ['favourites' => $favourites]);
    }

    // Método para obter o progresso dos livros do utilizador
    public function myBooksProgress()
    {
        $user = auth()->user();
        $books = $user->booksRead; // Obtém os livros com progresso

        return view('manage-my-books.progress', compact('books'));
    }

    // Método para obter o index dos crachás
    public function badgesIndex()
    {
        // Redireciona para a página de Book Badges
        return redirect()->route('book-badges');
    }

    // Método para obter os crachás do utilizador
    public function bookBadges()
    {
        $user = auth()->user();

        // Obtém livros com progresso >= 90%
        $badges = $user->booksRead()->wherePivot('progress', '>=', 90)->get();

        return view('manage-my-books.book-badges', compact('badges'));
    }

    // Método para obter as atividades do utilizador
    public function activityBadges()
    {
        $user = auth()->user();

        // Obter atividades com progresso 100% e suas imagens
        $badges = $user->activities()
            ->wherePivot('progress', '=', 100) // Filtra pelo progresso completo
            ->with('activityImages') // Carrega as imagens relacionadas
            ->get();

        return view('manage-my-books.activity-badges', compact('badges'));
    }
}

