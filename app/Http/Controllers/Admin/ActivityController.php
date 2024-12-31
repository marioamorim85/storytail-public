<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\ActivityImage;
use App\Models\Book;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    /**
     * Exibir todas as atividades.
     */
    public function index()
    {
        // Carregar todas as atividades com relacionamentos
        $activities = Activity::with(['books', 'activityImages'])->get();

        // Carregar todos os livros disponíveis para o filtro
        $books = Book::all();

        // Passar os dados para a view
        return view('admin.activities.index', compact('activities', 'books'));
    }


    /**
     * Formulário para criar uma nova atividade.
     */
    public function create()
    {
        $books = Book::all(); // Carregar todos os livros disponíveis
        return view('admin.activities.create', compact('books'));
    }

    /**
     * Armazenar uma nova atividade.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'book_id' => 'required|exists:books,id',
                'images' => 'required|array',
                'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:5120',
                'image_titles' => 'required|array',
                'image_titles.*' => 'required|string|max:255',
                'is_active' => 'boolean'
            ]);

            DB::beginTransaction();

            // Criar a atividade
            $activity = Activity::create([
                'title' => $request->title,
                'description' => $request->description,
                'is_active' => $request->boolean('is_active', false)
            ]);

            // Associar ao livro
            $activity->books()->attach($request->book_id);

            // Processar imagens
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $key => $image) {
                    $path = $image->store('activity_images', 'public');

                    $activity->activityImages()->create([
                        'title' => $request->image_titles[$key] ?? 'Activity Image ' . ($key + 1),
                        'image_url' => $path,
                        'order' => $key + 1
                    ]);
                }
            }

            DB::commit();

            return redirect()
                ->route('admin.activities.list')
                ->with('success', "Activity \"{$activity->title}\" has been created successfully!");

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('error', 'Please check the form and try again.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }


    /**
     * Exibir uma atividade.
     */
    public function show(string $id)
    {
        $activity = Activity::with(['books', 'activityImages'])->findOrFail($id);
        return view('admin.activities.show', compact('activity'));
    }

    /**
     * Formulário para editar uma atividade.
     */
    public function edit(string $id)
    {
        $activity = Activity::with(['books', 'activityImages'])->findOrFail($id);
        $books = Book::all(); // Carregar todos os livros disponíveis
        return view('admin.activities.edit', compact('activity', 'books'));
    }

    /**
     * Atualizar uma atividade existente.
     */
    public function update(Request $request, string $id)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'is_active' => 'boolean',
                'book_id' => 'required|exists:books,id',
                'images' => 'nullable|array',
                'images.*' => 'file|image|mimes:jpeg,png,jpg,gif|max:5120',
                'image_titles' => 'nullable|array',
                'image_titles.*' => 'nullable|string|max:255',
                'image_orders' => 'nullable|array',
                'image_orders.*' => 'nullable|integer|min:1',
            ]);

            DB::beginTransaction();

            // Encontrar a atividade
            $activity = Activity::findOrFail($id);

            // Atualizar a atividade
            $activity->update([
                'title' => $request->title,
                'description' => $request->description,
                'is_active' => $request->boolean('is_active', false),
            ]);

            // Associar ao livro (apenas um livro permitido)
            $activity->books()->sync([$request->book_id]);

            // Processar novas imagens
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $key => $image) {
                    $path = $image->store('activity_images', 'public');

                    $activity->activityImages()->create([
                        'title' => $request->image_titles[$key] ?? 'Activity Image ' . ($key + 1),
                        'image_url' => $path,
                        'order' => $request->image_orders[$key] ?? ($key + 1),
                    ]);
                }
            }

            DB::commit();

            return redirect()
                ->route('admin.activities.list')
                ->with('success', "Activity \"{$activity->title}\" has been updated successfully!");

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('error', 'Please check the form and try again.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'An error occurred while updating the activity: ' . $e->getMessage());
        }
    }


    /**
     * Excluir uma atividade.
     */
    public function destroy(string $id)
    {
        try {
            $activity = Activity::findOrFail($id);
            $activityTitle = $activity->title;

            DB::beginTransaction();

            // Eliminar imagens associadas
            foreach ($activity->activityImages as $image) {
                Storage::disk('public')->delete($image->image_url);
                $image->delete();
            }

            // Remover a associação com livros
            $activity->books()->detach();

            // Eliminar a atividade
            $activity->delete();

            DB::commit();

            return redirect()
                ->route('admin.activities.list')
                ->with('success', "Activity \"$activityTitle\" has been deleted successfully!");
        } catch (ModelNotFoundException $e) {
            return redirect()
                ->route('admin.activities.list')
                ->with('error', 'Activity not found.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error deleting activity: ' . $e->getMessage());

            return redirect()
                ->route('admin.activities.list')
                ->with('error', 'An error occurred while deleting the activity. Please try again.');
        }
    }

    public function removeImage($activityId, $imageId)
    {
        try {
            DB::beginTransaction();

            $activity = Activity::findOrFail($activityId);
            $image = ActivityImage::where('id', $imageId)
                ->where('activity_id', $activityId)
                ->firstOrFail();

            // Não excluímos o arquivo físico porque podemos precisar restaurar depois
            $image->delete(); // Isso irá usar soft delete

            DB::commit();

            return redirect()
                ->route('admin.activities.edit', $activityId)
                ->with('success', 'Image has been removed successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error removing activity image: ' . $e->getMessage());

            return redirect()
                ->route('admin.activities.edit', $activityId)
                ->with('error', 'Failed to remove the image. Please try again.');
        }
    }
}
