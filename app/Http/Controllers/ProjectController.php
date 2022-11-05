<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Project;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ProjectController extends Controller
{

    /**
     * Create a project.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function post(Request $request): JsonResponse
    {
        try {
            $post_data = $request->validate([
                'name' => 'required|string',
                'description' => 'string',
                'categories' => 'required|array',
            ]);
            $categories = Category::findManyOrFail($post_data['categories']);
            $project = new Project([
                'name' => $post_data['name'],
                'description' => $post_data['description'] ?? '',
            ]);
            $project->user()->associate(Auth::user());
            $project->save();
            $project->categories()->attach($categories);

            return response()->json(['id' => $project->id]);
        }
        catch (ValidationException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
        catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Update project.
     *
     * @param Request $request
     * @param int $id
     *
     * @return JsonResponse
     */
    public function patch(Request $request, int $id): JsonResponse {
        try {
            $project = Project::findOrFail($id);
            $post_data = $request->validate([
                'name' => 'string',
                'description' => 'string',
                'categories' => 'array',
            ]);
            if (!empty($post_data['name'])) {
                $project->name = $post_data['name'];
            }
            if (isset($post_data['description'])) {
                $project->description = $post_data['description'];
            }
            if (!empty($post_data['categories'])) {
                $categories = Category::findManyOrFail($post_data['categories']);
                $project->categories()->sync($categories);
            }
            $project->save();

            return response()->json(['id' => $project->id]);
        }
        catch (ValidationException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
        catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Project is not found'], 404);
        }
        catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Delete project.
     *
     * @param int $id
     *
     * @return JsonResponse
     */
    public function delete(int $id): JsonResponse
    {
        try {
            Project::destroy($id);
            return response()->json();
        }
        catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get the project.
     *
     * @param $id
     *
     * @return JsonResponse
     */
    public function get($id): JsonResponse {
        try {
            $project = Project::findOrFail($id);
            return response()->json($project->toArray());
        }
        catch (ModelNotFoundException $e) {
                return response()->json(['error' => 'Project is not found'], 404);
            }
        catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get all projects.
     *
     * @return JsonResponse
     */
    public function getAll(): JsonResponse
    {
        try {
            return response()->json(Project::all());
        }
        catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

}
