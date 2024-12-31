<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AgeGroup;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;

class AgeGroupController extends Controller
{
    public function index()
    {
        $ageGroups = AgeGroup::all();
        return view('admin.age-groups.index', compact('ageGroups'));
    }

    public function create()
    {
        return view('admin.age-groups.create');
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'age_group' => 'required|string|max:255'
            ]);

            $ageGroup = AgeGroup::create([
                'age_group' => $request->age_group
            ]);

            return redirect()
                ->route('admin.books.age-groups.list')
                ->with('success', "Age Group \"$ageGroup->age_group\" has been created successfully!");

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()
                ->back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('error', 'Please check the form and try again.');

        } catch (\Exception $e) {
            Log::error('Error creating age group: ' . $e->getMessage());

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to create age group. Please try again.');
        }
    }

    public function edit($id)
    {
        try {
            $ageGroup = AgeGroup::findOrFail($id);
            return view('admin.age-groups.edit', compact('ageGroup'));

        } catch (ModelNotFoundException $e) {
            return redirect()
                ->route('admin.books.age-groups.list')
                ->with('error', 'Age Group not found.');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'age_group' => 'required|string|max:255'
            ]);

            $ageGroup = AgeGroup::findOrFail($id);
            $oldName = $ageGroup->age_group;

            $ageGroup->age_group = $request->age_group;
            $ageGroup->save();

            $message = $oldName !== $request->age_group
                ? "Age Group \"$oldName\" has been renamed to \"$request->age_group\" successfully!"
                : "Age Group \"$request->age_group\" has been updated successfully!";

            return redirect()
                ->route('admin.books.age-groups.list')
                ->with('success', $message);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()
                ->back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('error', 'Please check the form and try again.');

        } catch (ModelNotFoundException $e) {
            return redirect()
                ->route('admin.books.age-groups.list')
                ->with('error', 'Age Group not found.');

        } catch (\Exception $e) {
            Log::error('Error updating age group: ' . $e->getMessage());

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to update age group. Please try again.');
        }
    }

    public function destroy($id)
    {
        try {
            $ageGroup = AgeGroup::findOrFail($id);
            $ageGroupName = $ageGroup->age_group;

            // Verifica se existem livros usando este grupo de idade
            if ($ageGroup->books()->exists()) {
                throw new \Exception('Cannot delete age group that has books associated with it.');
            }

            $ageGroup->delete();

            return redirect()
                ->route('admin.books.age-groups.list')
                ->with('success', "Age Group \"$ageGroupName\" has been deleted successfully!");

        } catch (ModelNotFoundException $e) {
            return redirect()
                ->route('admin.books.age-groups.list')
                ->with('error', 'Age Group not found.');

        } catch (\Exception $e) {
            Log::error('Error deleting age group: ' . $e->getMessage());

            return redirect()
                ->route('admin.books.age-groups.list')
                ->with('error', $e->getMessage() ?: 'Failed to delete age group. Please try again.');
        }
    }
}
