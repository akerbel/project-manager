<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    use TryCatchTrait;

    /**
     * Get model name of the controller.
     *
     * @return string
     */
    protected function getModelName(): string
    {
        return 'project';
    }

    /**
     * Create a project.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function post(Request $request): JsonResponse
    {
        return $this->tryCatch(function() use ($request) {
            $this->authorize('create', Project::class);
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
        });
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
        return $this->tryCatch(function() use ($request, $id) {
            $project = Project::findOrFail($id);
            $this->authorize('update', $project);
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
        });
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
        return $this->tryCatch(function() use ($id) {
            $project = Project::findOrFail($id);
            $this->authorize('delete', $project);
            Project::destroy($id);
            return response()->json();
        });
    }

    /**
     * Get the project.
     *
     * @param $id
     *
     * @return JsonResponse
     */
    public function get($id): JsonResponse {
        return $this->tryCatch(function() use ($id) {
            $project = Project::findOrFail($id);
            $this->authorize('view', $project);
            return response()->json($project->toArray());
        });
    }

    /**
     * Get all projects.
     *
     * @return JsonResponse
     */
    public function getAll(): JsonResponse
    {
        return $this->tryCatch(function() {
            $this->authorize('viewAny', Project::class);
            $user = Auth::user();
            if ($user->isAdmin()) {
                $result = Project::all();
            }
            else {
                $result = Project::where('user_id', $user->id)->get();
            }

            return response()->json($result);
        });
    }

}
