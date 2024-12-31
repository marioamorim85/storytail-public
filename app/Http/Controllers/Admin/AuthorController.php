<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Author;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class AuthorController extends Controller
{
    public function index()
    {
        $authors = Author::with('books')->get();
        return view('admin.authors.index', compact('authors'));
    }

    public function create()
    {
        return view('admin.authors.create');
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'nationality' => 'nullable|string|max:255',
                'author_photo_url' => 'nullable|image|mimes:jpeg,png,jpg|max:5120'
            ]);

            DB::beginTransaction();

            $data = $request->except('author_photo_url');

            if ($request->hasFile('author_photo_url')) {
                $data['author_photo_url'] = $request->file('author_photo_url')->store('authors', 'public');
            }

            $author = Author::create($data);

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'author' => [
                        'id' => $author->id,
                        'first_name' => $author->first_name,
                        'last_name' => $author->last_name,
                        'full_name' => "{$author->first_name} {$author->last_name}"
                    ],
                    'message' => "Author \"{$author->first_name} {$author->last_name}\" has been created successfully!"
                ]);
            }


            return redirect()
                ->route('admin.authors.list')
                ->with('success', "Author \"{$author->name}\" has been created successfully!");

        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 422);
            }

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'An error occurred while creating the author.');
        }
    }

    public function show($id)
    {
        try {
            $author = Author::with('books')->findOrFail($id);
            return view('admin.authors.show', compact('author'));
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.authors.list')
                ->with('error', 'Author not found.');
        }
    }

    public function edit($id)
    {
        try {
            $author = Author::with('books')->findOrFail($id);
            return view('admin.authors.edit', compact('author'));
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.authors.list')
                ->with('error', 'Author not found.');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'nationality' => 'nullable|string|max:255',
                'author_photo_url' => 'nullable|image|mimes:jpeg,png,jpg|max:5120'
            ]);

            DB::beginTransaction();

            $author = Author::findOrFail($id);
            $oldName = $author->name;

            $data = $request->except('author_photo_url');

            // Upload da nova foto se fornecida
            if ($request->hasFile('author_photo_url')) {
                // Remover foto antiga se existir
                if ($author->author_photo_url) {
                    Storage::disk('public')->delete($author->author_photo_url);
                }
                $data['author_photo_url'] = $request->file('author_photo_url')->store('authors', 'public');
            }

            $author->update($data);

            DB::commit();

            $message = $oldName !== $author->name
                ? "Author \"$oldName\" has been renamed to \"{$author->name}\" and updated successfully!"
                : "Author \"{$author->name}\" has been updated successfully!";

            return redirect()
                ->route('admin.authors.list')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating author: ' . $e->getMessage());

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'An error occurred while updating the author. Please try again.');
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $author = Author::findOrFail($id);
            $name = $author->name;

            // Verificar se autor tem livros antes de deletar
            if ($author->books()->exists()) {
                throw new \Exception('Cannot delete author with associated books.');
            }

            // Remover foto se existir
            if ($author->author_photo_url) {
                Storage::disk('public')->delete($author->author_photo_url);
            }

            $author->delete();

            DB::commit();

            return redirect()
                ->route('admin.authors.list')
                ->with('success', "Author \"$name\" has been deleted successfully!");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting author: ' . $e->getMessage());

            $errorMessage = $e->getMessage() === 'Cannot delete author with associated books.'
                ? 'Cannot delete author with associated books. Remove the book associations first.'
                : 'An error occurred while deleting the author. Please try again.';

            return redirect()
                ->route('admin.authors.list')
                ->with('error', $errorMessage);
        }
    }

    public function removePhoto($id)
    {
        try {
            DB::beginTransaction();

            $author = Author::findOrFail($id);

            if ($author->author_photo_url) {
                Storage::disk('public')->delete($author->author_photo_url);
                $author->update(['author_photo_url' => null]);
            }

            DB::commit();

            return redirect()
                ->route('admin.authors.edit', $id)
                ->with('success', 'Author photo has been removed successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error removing author photo: ' . $e->getMessage());

            return redirect()
                ->route('admin.authors.edit', $id)
                ->with('error', 'Failed to remove author photo. Please try again.');
        }
    }
}
