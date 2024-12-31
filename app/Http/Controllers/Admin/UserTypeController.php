<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserType;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class UserTypeController extends Controller
{
    public function index()
    {
        $userTypes = UserType::all();
        return view('admin.user-types.index', compact('userTypes'));
    }

    public function create()
    {
        return view('admin.user-types.create');
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'user_type' => 'required|string|max:255|unique:user_types,user_type',
            ]);

            DB::beginTransaction();

            $userType = UserType::create([
                'user_type' => $request->user_type,
            ]);

            DB::commit();

            return redirect()
                ->route('admin.users.user-types.list')
                ->with('success', "User Type \"$userType->user_type\" has been created successfully!");

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('error', 'Please check the form and try again.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating user type: ' . $e->getMessage());

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to create user type. Please try again.');
        }
    }


    public function edit($id)
    {
        try {
            $userType = UserType::findOrFail($id);
            return view('admin.user-types.edit', compact('userType'));

        } catch (ModelNotFoundException $e) {
            return redirect()
                ->route('admin.users.user-types.list')
                ->with('error', 'User Type not found.');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $userType = UserType::findOrFail($id);

            $request->validate([
                'user_type' => 'required|string|max:255|unique:user_types,user_type,' . $id,
            ]);

            DB::beginTransaction();

            $oldUserType = $userType->user_type;

            $userType->user_type = $request->user_type;
            $userType->save();

            $message = ($oldUserType !== $request->user_type)
                ? "User Type renamed from \"$oldUserType\" to \"$request->user_type\" successfully!"
                : "User Type \"$request->user_type\" has been updated successfully!";

            DB::commit();

            return redirect()
                ->route('admin.users.user-types.list')
                ->with('success', $message);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('error', 'Please check the form and try again.');

        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return redirect()
                ->route('admin.users.user-types.list')
                ->with('error', 'User Type not found.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating user type: ' . $e->getMessage());

            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }


    public function destroy($id)
    {
        try {
            $userType = UserType::findOrFail($id);

            // Verifica se há utilizadores associados ao User Type
            if ($userType->users()->count() > 0) {
                throw new \Exception("Cannot delete User Type that is assigned to users.");
            }

            DB::beginTransaction();

            $userTypeName = $userType->name;
            $userType->delete();

            DB::commit();

            return redirect()
                ->route('admin.users.user-types.list')
                ->with('success', "User Type \"$userTypeName\" has been deleted successfully!");

        } catch (ModelNotFoundException $e) {
            return redirect()
                ->route('admin.users.user-types.list')
                ->with('error', 'User Type not found.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting user type: ' . $e->getMessage());

            return redirect()
                ->route('admin.users.user-types.list')
                ->with('error', $e->getMessage() ?: 'Failed to delete user type. Please try again.');
        }
    }
}
