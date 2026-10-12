<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

abstract class CrudController extends Controller
{
    protected string $model;

    abstract protected function rules(bool $updating = false): array;

    public function index()
    {
        $model = $this->model;
        return response()->json([
            class_basename($model) => $model::query()->latest()->get(),
        ], 200);
    }

    public function find(string $id)
    {
        $model = $this->model;
        $record = $model::query()->find($id);

        if (!$record) {
            return response()->json([
                'message' => class_basename($model) . ' not found.',
            ], 404);
        }

        return response()->json([
            class_basename($model) => $record,
        ], 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->rules());
        $model = $this->model;
        $record = $model::query()->create($validated);
        return response()->json([
            'message' => class_basename($model) . ' created successfully.',
            class_basename($model) => $record,
        ], 201);
    }

    public function update(Request $request, string $id)
    {
        $model = $this->model;
        $record = $model::query()->findOrFail($id);
        $record->update($request->validate($this->rules(true)));
        return response()->json([
            'message' => class_basename($model) . ' updated successfully.',
            class_basename($model) => $record->fresh(),
        ], 200);
    }

    public function delete(string $id)
    {
        $model = $this->model;
        $record = $model::query()->find($id);

        if (!$record) {
            return response()->json([
                'message' => class_basename($model) . ' not found.',
            ], 404);
        }

        $record->delete();

        return response()->json([
            'message' => class_basename($model) . ' deleted successfully.',
        ], 200);
    }

    // Keep the standard Laravel resource method names available for existing callers.
    public function show(string $id)
    {
        return $this->find($id);
    }

    public function destroy(string $id)
    {
        return $this->delete($id);
    }

    protected function required(bool $updating): string
    {
        return $updating ? 'sometimes|required' : 'required';
    }
}
