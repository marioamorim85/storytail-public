<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TagController extends Controller
{
    public function index()
    {
        $tags = Tag::all();
        return view('admin.tags.index', compact('tags'));
    }

    public function create()
    {
        return view('admin.tags.create');
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
            ]);

            $tag = Tag::create([
                'name' => $request->name
            ]);

            return redirect()
                ->route('admin.books.tags.list')
                ->with('success', "Tag \"$tag->name\" has been created successfully!");

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()
                ->back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('error', 'Please check the form and try again.');

        } catch (\Exception $e) {
            Log::error('Error creating tag: ' . $e->getMessage());

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to create tag. Please try again.');
        }
    }

    public function edit($id)
    {
        try {
            $tag = Tag::findOrFail($id);
            return view('admin.tags.edit', compact('tag'));

        } catch (ModelNotFoundException $e) {
            return redirect()
                ->route('admin.books.tags.list')
                ->with('error', 'Tag not found.');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
            ]);

            $tag = Tag::findOrFail($id);
            $oldName = $tag->name;

            $tag->name = $request->name;
            $tag->save();

            $message = $oldName !== $request->name
                ? "Tag \"$oldName\" has been renamed to \"$request->name\" successfully!"
                : "Tag \"$request->name\" has been updated successfully!";

            return redirect()
                ->route('admin.books.tags.list')
                ->with('success', $message);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()
                ->back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('error', 'Please check the form and try again.');

        } catch (ModelNotFoundException $e) {
            return redirect()
                ->route('admin.books.tags.list')
                ->with('error', 'Tag not found.');

        } catch (\Exception $e) {
            Log::error('Error updating tag: ' . $e->getMessage());

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to update tag. Please try again.');
        }
    }

    public function destroy($id)
    {
        try {
            $tag = Tag::findOrFail($id);
            $tagName = $tag->name;

            $tag->delete();

            return redirect()
                ->route('admin.books.tags.list')
                ->with('success', "Tag \"$tagName\" has been deleted successfully!");

        } catch (ModelNotFoundException $e) {
            return redirect()
                ->route('admin.books.tags.list')
                ->with('error', 'Tag not found.');

        } catch (\Exception $e) {
            Log::error('Error deleting tag: ' . $e->getMessage());

            return redirect()
                ->route('admin.books.tags.list')
                ->with('error', 'Failed to delete tag. Please try again.');
        }
    }

}
