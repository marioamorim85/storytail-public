<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Video;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Author;
use App\Models\Tag;
use Illuminate\Http\Request;
use App\Models\Book;
use App\Models\AgeGroup;
use App\Models\Page;


class BookController extends Controller
{
    public function index()
    {
        $books = Book::with('ageGroup', 'authors')->get()->map(function ($book) {
            $book->cover_url = $book->cover_url ? Storage::url($book->cover_url) : null;
            return $book;
        });
        $ageGroups = AgeGroup::all();

        return view('admin.books.index', compact('books', 'ageGroups'));
    }


    public function create()
    {
        $ageGroups = AgeGroup::all();
        $authors = Author::all();
        $tags = Tag::all();

        return view('admin.books.create', compact('ageGroups', 'authors', 'tags'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'read_time' => 'required|integer|min:1',
                'age_group_id' => 'required|exists:age_groups,id',
                'authors' => 'required|array',
                'authors.*' => 'exists:authors,id',
                'tags' => 'required|array',
                'tags.*' => 'exists:tags,id',
                'cover_url' => 'required|file|image|mimes:jpeg,png,jpg,gif,svg|max:131072',
                'pages' => 'required|array',
                'pages.*' => 'file|image|mimes:jpeg,png,jpg|max:131072',
                'page_index' => 'required|array',
                'page_index.*' => 'required|integer|min:1',
                'video_url' => ['nullable', 'url', 'regex:/^(https?:\/\/)?(www\.)?(youtube\.com|youtu\.be)\/.+$/']
            ], [
                'title.required' => 'The title field is required.',
                'description.required' => 'The description field is required.',
                'read_time.required' => 'The read time field is required.',
                'read_time.integer' => 'The read time must be an integer.',
                'read_time.min' => 'The read time must be at least 1 minute.',
                'age_group_id.required' => 'Please select an age group.',
                'age_group_id.exists' => 'The selected age group is invalid.',
                'authors.required' => 'At least one author must be selected.',
                'authors.*.exists' => 'One or more authors are invalid.',
                'tags.required' => 'At least one tag must be selected.',
                'tags.*.exists' => 'One or more tags are invalid.',
                'cover_url.required' => 'The cover image is required.',
                'cover_url.file' => 'The cover must be a valid file.',
                'cover_url.image' => 'The cover must be an image.',
                'cover_url.mimes' => 'The cover must be a file of type: jpeg, png, jpg, gif, svg.',
                'cover_url.max' => 'The cover may not be greater than 128MB.',
                'pages.required' => 'At least one page image is required.',
                'pages.*.file' => 'Each page must be a valid file.',
                'pages.*.image' => 'Each page must be an image.',
                'pages.*.mimes' => 'Each page must be a file of type: jpeg, png, jpg.',
                'pages.*.max' => 'Each page may not be greater than 128MB.',
                'page_index.required' => 'Each page must have an order index.',
                'page_index.*.integer' => 'Each page index must be an integer.',
                'page_index.*.min' => 'Each page index must be at least 1.',
                'video.file' => 'The video must be a valid file.',
                'video.mimes' => 'The video must be a file of type: mp4, mov, ogg, qt.',
                'video.max' => 'The video may not be greater than 20MB.',
            ]);

            // Iniciar transação
            DB::beginTransaction();

            // Upload da imagem da capa
            $coverPath = null;
            if ($request->hasFile('cover_url')) {
                $coverPath = $request->file('cover_url')->store('covers', 'public');
            }

            // Criar o livro
            $book = Book::create([
                'title' => $request->title,
                'description' => $request->description,
                'read_time' => $request->read_time,
                'age_group_id' => $request->age_group_id,
                'cover_url' => $coverPath,
                'is_active' => $request->boolean('is_active', false),
                'access_level' => $request->access_level,
            ]);

            // Relacionar autores e tags
            $book->authors()->sync($request->authors);
            if ($request->has('tags')) {
                $book->tags()->sync($request->tags);
            }

            // Upload das páginas do livro
            if ($request->hasFile('pages')) {
                $pages = $request->file('pages');
                $pageIndexes = $request->page_index;

                foreach ($pages as $key => $page) {
                    $path = $page->store('book_pages', 'public');
                    $book->pages()->create([
                        'page_image_url' => $path,
                        'page_index' => $pageIndexes[$key] ?? ($key + 1),
                    ]);
                }
            }

            // Upload do vídeo
            if ($request->video_url) {
                Video::updateOrCreate(
                    ['book_id' => $book->id],
                    [
                        'title' => $book->title . ' - Video',
                        'video_url' => $request->video_url
                    ]
                );
            }

            // Commit da transação
            DB::commit();
            return redirect()
                ->route('admin.books.list')
                ->with('success', '"' . $book->title . '" has been created successfully!');

        } catch (ValidationException $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('error', 'Please check the form and try again.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error creating book: ' . $e->getMessage());

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'An error occurred while creating the book. Please try again.');
        }
    }


    public function show($id)
    {
        $book = Book::with(['activities', 'authors', 'tags', 'pages', 'ageGroup', 'video'])->findOrFail($id);

        return view('admin.books.show', compact('book'));
    }


    public function edit(Book $book)
    {
        $book->load(['pages' => function ($query) {
            $query->withTrashed(); // Inclui páginas excluídas
        }]);

        $authors = Author::all();
        $tags = Tag::all();
        $ageGroups = AgeGroup::all();

        return view('admin.books.edit', compact('book', 'authors', 'tags', 'ageGroups'));
    }


    public function update(Request $request, string $id)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'read_time' => 'required|integer|min:1',
                'age_group_id' => 'required|exists:age_groups,id',
                'authors' => 'required|array',
                'authors.*' => 'exists:authors,id',
                'tags' => 'required|array',
                'tags.*' => 'exists:tags,id',
                'cover_url' => $request->hasFile('cover_url') ? 'required|file|image|mimes:jpeg,png,jpg,gif,svg|max:131072' : 'nullable', // 128MB
                'pages' => $request->hasFile('pages') ? 'required|array' : 'nullable',
                'pages.*' => $request->hasFile('pages') ? 'file|image|mimes:jpeg,png,jpg|max:131072' : 'nullable', // 128MB
                'page_index' => $request->has('page_index') ? 'required|array' : 'nullable',
                'page_index.*' => $request->has('page_index') ? 'required|integer|min:1' : 'nullable',
                'video_url' => ['nullable', 'url', 'regex:/^(https?:\/\/)?(www\.)?(youtube\.com|youtu\.be)\/.+$/']
            ]);

            DB::beginTransaction();

            $book = Book::findOrFail($id);

            // Guardar o título antigo para a mensagem
            $oldTitle = $book->title;

            $book->title = $request->title;
            $book->description = $request->description;
            $book->read_time = $request->read_time;
            $book->age_group_id = $request->age_group_id;
            $book->is_active = $request->boolean('is_active', false);
            $book->access_level = $request->access_level;

            // Processar nova imagem se fornecida
            if ($request->hasFile('cover_url')) {
                // Deletar imagem antiga se existir
                if ($book->cover_url) {
                    Storage::disk('public')->delete($book->cover_url);
                }
                $book->cover_url = $request->file('cover_url')->store('covers', 'public');
            }

            // Sincronizar relacionamentos
            $book->authors()->sync($request->authors);
            $book->tags()->sync($request->tags);

            // Atualizar as páginas se houver novas páginas
            if ($request->hasFile('pages')) {
                foreach ($request->file('pages') as $index => $page) {
                    $pagePath = $page->store('pages', 'public');
                    $book->pages()->create([
                        'page_image_url' => $pagePath,
                        'page_index' => $request->page_index[$index],
                    ]);
                }
            }

            // Remover páginas se o parâmetro 'remove_pages' estiver presente no request
            if ($request->has('remove_pages')) {
                $removePageIds = $request->remove_pages;

                foreach ($removePageIds as $pageId) {
                    $page = $book->pages()->find($pageId);
                    if ($page) {
                        Storage::disk('public')->delete($page->page_image_url);
                        $page->delete();
                    }
                }
            }

            // Atualizar o vídeo
            if ($request->has('video_url') && $request->video_url) {
                $book->video()->updateOrCreate(
                    ['book_id' => $book->id],
                    ['video_url' => $request->video_url]
                );
            }

            $book->save();
            DB::commit();

            $message = $oldTitle !== $request->title
                ? "Book \"$oldTitle\" has been renamed to \"$request->title\" and updated successfully!"
                : "Book \"$request->title\" has been updated successfully!";

            return redirect()
                ->route('admin.books.list')
                ->with('success', $message);

        } catch (ValidationException $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('error', 'Please check the form and try again.');

        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return redirect()
                ->route('admin.books.list')
                ->with('error', 'Book not found.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating book: ' . $e->getMessage());

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'An error occurred while updating the book. Please try again.');
        }
    }



    public function destroy(string $id)
    {
        try {
            $book = Book::findOrFail($id);
            $bookTitle = $book->title; // Guardar o título antes de deletar

            // Iniciar transação
            DB::beginTransaction();

            // Deletar arquivos associados
            if ($book->cover_url) {
                Storage::disk('public')->delete($book->cover_url);
            }

            // Deletar páginas e suas imagens
            foreach ($book->pages as $page) {
                Storage::disk('public')->delete($page->page_image_url);
            }

            // Deletar o livro
            $book->delete();

            // Commit da transação
            DB::commit();

            return redirect()
                ->route('admin.books.list')
                ->with('success', "Book \"$bookTitle\" has been deleted successfully!");

        } catch (ModelNotFoundException $e) {
            return redirect()
                ->route('admin.books.list')
                ->with('error', 'Book not found.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error deleting book: ' . $e->getMessage());

            return redirect()
                ->route('admin.books.list')
                ->with('error', 'Failed to delete the book. Please try again.');
        }
    }

    public function showMessage()
    {
        return view('admin.books.message');
    }

    public function removePage(Request $request, Book $book, Page $page)
    {
        try {
            DB::beginTransaction();

            // Verificar se a página pertence ao livro
            if ($page->book_id !== $book->id) {
                throw new \Exception('This page does not belong to the selected book.');
            }

            // Guardar o índice da página para a mensagem
            $pageIndex = $page->page_index;

            // Soft delete da página
            $page->delete(); // Isso marca o campo `deleted_at` sem remover fisicamente

            // Reordenar as páginas restantes se necessário
            $book->pages()
                ->where('page_index', '>', $pageIndex)
                ->orderBy('page_index')
                ->get()
                ->each(function ($p, $index) {
                    $p->update(['page_index' => $p->page_index - 1]);
                });

            DB::commit();

            return redirect()
                ->route('admin.books.edit', $book->id)
                ->with('success', "Page $pageIndex of \"$book->title\" has been removed successfully!");

        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return redirect()
                ->route('admin.books.edit', $book->id)
                ->with('error', 'Page not found.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error removing page: ' . $e->getMessage());

            return redirect()
                ->route('admin.books.edit', $book->id)
                ->with('error', 'Failed to remove the page. Please try again.');
        }
    }


    public function showActivities($id)
    {
        try {
            $book = Book::with('activities')->findOrFail($id);

            return view('admin.books.activities', compact('book'));
        } catch (ModelNotFoundException $e) {
            return redirect()->route('admin.books.list')->with('error', 'Book not found.');
        }
    }

}
