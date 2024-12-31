<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Books",
 *     description="APIs para gestão e consulta de livros"
 * )
 *
 * @OA\Schema(
 *     schema="Book",
 *     type="object",
 *     description="Modelo de Livro",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="title", type="string", example="O Grande Livro"),
 *     @OA\Property(property="description", type="string", example="Descrição detalhada do livro"),
 *     @OA\Property(property="read_time", type="integer", example=15),
 *     @OA\Property(property="access_level", type="integer", example=1),
 *     @OA\Property(property="is_active", type="boolean", example=true),
 *     @OA\Property(property="cover_url", type="string", example="covers/book.jpg"),
 *     @OA\Property(property="video_url", type="string", nullable=true),
 *     @OA\Property(property="age_group_id", type="integer", example=1)
 * )
 */
class BookController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/books",
     *     summary="Listar livros com filtros",
     *     description="Retorna lista de livros com base nos filtros fornecidos",
     *     tags={"Books"},
     *     @OA\Parameter(
     *         name="author_name",
     *         in="query",
     *         description="Filtrar por nome do autor",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="title",
     *         in="query",
     *         description="Filtrar por título do livro",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="author_id",
     *         in="query",
     *         description="Filtrar por ID do autor",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="age_group_id",
     *         in="query",
     *         description="Filtrar por faixa etária",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="access_level",
     *         in="query",
     *         description="Filtrar por nível de acesso (1=Gratuito, 2=Premium)",
     *         @OA\Schema(type="integer", enum={1, 2})
     *     ),
     *     @OA\Parameter(
     *         name="is_active",
     *         in="query",
     *         description="Filtrar por estado ativo",
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Parameter(
     *         name="activity_id",
     *         in="query",
     *         description="Filtrar por atividade relacionada",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="tag_id",
     *         in="query",
     *         description="Filtrar por tag",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="min_read_time",
     *         in="query",
     *         description="Tempo mínimo de leitura (minutos)",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="max_read_time",
     *         in="query",
     *         description="Tempo máximo de leitura (minutos)",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Lista de livros",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="O Grande Livro"),
     *                 @OA\Property(property="description", type="string"),
     *                 @OA\Property(property="access_level", type="integer", example=1),
     *                 @OA\Property(property="is_active", type="boolean", example=true),
     *                 @OA\Property(property="age_group_id", type="integer", example=1),
     *                 @OA\Property(property="read_time", type="integer", example=15),
     *                 @OA\Property(property="cover_url", type="string"),
     *                 @OA\Property(
     *                     property="authors",
     *                     type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="integer"),
     *                         @OA\Property(property="name", type="string")
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="age_group",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="name", type="string")
     *                 ),
     *                 @OA\Property(
     *                     property="tags",
     *                     type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="integer"),
     *                         @OA\Property(property="name", type="string")
     *                     )
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function listBooks(Request $request)
    {
        $query = Book::with(['authors', 'ageGroup', 'activities', 'tags'])
            ->select('books.id', 'books.title', 'books.description', 'books.access_level',
                'books.is_active', 'books.age_group_id', 'books.read_time',
                'books.cover_url');

        // Filtro por nome do autor
        $query->when($request->author_name, function ($q, $authorName) {
            $q->whereHas('authors', function ($query) use ($authorName) {
                $query->where('first_name', 'like', "%{$authorName}%")
                    ->orWhere('last_name', 'like', "%{$authorName}%");
            });
        });

        // Filtro por título
        $query->when($request->title, function ($q, $title) {
            $q->where('title', 'like', "%{$title}%");
        });

        // Filtro por autor ID
        $query->when($request->author_id, function ($q, $authorId) {
            $q->whereHas('authors', function ($query) use ($authorId) {
                $query->where('authors.id', $authorId);
            });
        });

        // Filtro por grupo etário
        $query->when($request->age_group_id, function ($q, $ageGroupId) {
            $q->where('age_group_id', $ageGroupId);
        });

        // Filtro por nível de acesso
        $query->when($request->access_level, function ($q, $accessLevel) {
            $q->where('access_level', $accessLevel);
        });

        // Filtro por estado ativo
        $query->when(!is_null($request->is_active), function ($q) use ($request) {
            $q->where('is_active', $request->is_active);
        });

        // Filtros de tempo de leitura
        $query->when($request->min_read_time, function ($q, $minTime) {
            $q->where('read_time', '>=', $minTime);
        });

        $query->when($request->max_read_time, function ($q, $maxTime) {
            $q->where('read_time', '<=', $maxTime);
        });

        // Filtro por atividade
        $query->when($request->activity_id, function ($q, $activityId) {
            $q->whereHas('activities', function ($query) use ($activityId) {
                $query->where('activities.id', $activityId);
            });
        });

        // Filtro por tag
        $query->when($request->tag_id, function ($q, $tagId) {
            $q->whereHas('tags', function ($query) use ($tagId) {
                $query->where('tags.id', $tagId);
            });
        });

        return response()->json($query->get());
    }

    /**
     * @OA\Get(
     *     path="/api/books/{id}",
     *     summary="Detalhes do livro",
     *     description="Retorna os detalhes completos de um livro específico",
     *     tags={"Books"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID do livro",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Detalhes do livro",
     *         @OA\JsonContent(ref="#/components/schemas/Book")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Livro não encontrado"
     *     )
     * )
     */
    public function show($id)
    {
        try {
            $book = Book::with([
                'authors',
                'ageGroup',
                'activities',
                'tags',
                'pages',
                'userFavorite',
                'avgRating'
            ])->findOrFail($id);

            $book->averageRating = $book->avgRating->avg('rating') ?? 0;

            $relatedBooks = Book::with([
                'authors', 'ageGroup', 'activities',
                'tags', 'pages', 'userFavorite', 'avgRating'
            ])
                ->where('age_group_id', $book->age_group_id)
                ->where('id', '!=', $book->id)
                ->take(4)
                ->get();

            return response()->json([
                'book' => $book,
                'related_books' => $relatedBooks
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['message' => 'Livro não encontrado'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao processar pedido'], 500);
        }
    }

    public function filter(Request $request)
    {
        $response = $this->listBooks($request);
        $books = collect($response->original);  // Converte para collection
        return view('api_views.partials.books-list', ['books' => $books]);
    }

    public function index(Request $request)
    {
        $books = Book::with([])
            ->where('books.is_active', true)
            ->get();

        return view('home', compact('books'));
    }
}
