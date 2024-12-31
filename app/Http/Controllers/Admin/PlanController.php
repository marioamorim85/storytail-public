<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Plan;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PlanController extends Controller
{
    public function index()
    {
        $plans = Plan::withCount('subscriptions')->get();
        return view('admin.plans.index', compact('plans'));
    }

    public function create()
    {
        return view('admin.plans.create');
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255|unique:plans,name',
                'access_level' => 'required|integer|in:' . implode(',', array_keys(Plan::getAccessLevels())),
            ]);

            DB::beginTransaction();

            $plan = Plan::create([
                'name' => $request->name,
                'access_level' => $request->access_level
            ]);

            DB::commit();

            return redirect()
                ->route('admin.users.plans.list')
                ->with('success', "Plan \"$plan->name\" has been created successfully!");

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return redirect()
                ->back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('error', 'Please check the form and try again.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating plan: ' . $e->getMessage());

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to create plan. Please try again.');
        }
    }

    public function edit($id)
    {
        try {
            $plan = Plan::withCount('subscriptions')->findOrFail($id);
            return view('admin.plans.edit', compact('plan'));

        } catch (ModelNotFoundException $e) {
            return redirect()
                ->route('admin.users.plans.list')
                ->with('error', 'Plan not found.');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $plan = Plan::findOrFail($id);

            // Impedir alteração dos planos base (FREE e PREMIUM)
            if ($plan->id <= 2) {
                throw new \Exception('Cannot modify base plans (Free and Premium).');
            }

            $request->validate([
                'name' => 'required|string|max:255|unique:plans,name,' . $id,
                'access_level' => 'required|integer|in:' . implode(',', array_keys(Plan::getAccessLevels())),
            ]);

            DB::beginTransaction();

            $oldName = $plan->name;
            $oldAccessLevel = $plan->access_level;

            $plan->name = $request->name;
            $plan->access_level = $request->access_level;
            $plan->save();

            // Construir mensagem baseada nas mudanças
            $changes = [];
            if ($oldName !== $request->name) {
                $changes[] = "renamed from \"$oldName\" to \"$request->name\"";
            }
            if ($oldAccessLevel !== $request->access_level) {
                $changes[] = "access level updated to " . ($request->access_level === Plan::PREMIUM ? "Premium" : "Free");
            }

            $message = $changes
                ? "Plan has been " . implode(' and ', $changes) . " successfully!"
                : "Plan \"$request->name\" has been updated successfully!";

            DB::commit();

            return redirect()
                ->route('admin.users.plans.list')
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
                ->route('admin.users.plans.list')
                ->with('error', 'Plan not found.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating plan: ' . $e->getMessage());

            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $plan = Plan::withCount('subscriptions')->findOrFail($id);

            // Impedir exclusão dos planos base (FREE e PREMIUM)
            if ($plan->id <= 2) {
                throw new \Exception('Cannot delete base plans (Free and Premium).');
            }

            // Verifica se existem subscrições usando este plano
            if ($plan->subscriptions_count > 0) {
                throw new \Exception("Cannot delete plan that has {$plan->subscriptions_count} active subscriptions.");
            }

            DB::beginTransaction();

            $planName = $plan->name;
            $plan->delete();

            DB::commit();

            return redirect()
                ->route('admin.users.plans.list')
                ->with('success', "Plan \"$planName\" has been deleted successfully!");

        } catch (ModelNotFoundException $e) {
            return redirect()
                ->route('admin.users.plans.list')
                ->with('error', 'Plan not found.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting plan: ' . $e->getMessage());

            return redirect()
                ->route('admin.users.plans.list')
                ->with('error', $e->getMessage() ?: 'Failed to delete plan. Please try again.');
        }
    }
}
