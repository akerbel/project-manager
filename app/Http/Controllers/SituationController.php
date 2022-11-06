<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Situation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class SituationController extends Controller
{
    use TryCatchTrait;

    /**
     * Get model name of the controller.
     *
     * @return string
     */
    protected function getModelName(): string
    {
        return 'situation';
    }

    /**
     * Create a situation.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function post(Request $request): JsonResponse
    {
        return $this->tryCatch(function() use ($request) {
            $this->authorize('create', Situation::class);

            // Validate data.
            $post_data = $request->validate([
                'name' => 'required|string',
                'description' => 'string',
                'status' => 'required|int',
                'project_id' => 'required|int',
            ]);
            if (!Situation::isStatusAllowed($post_data['status'])) {
                throw ValidationException::withMessages(['status' => 'Status is not allowed']);
            }

            // Validate project.
            $project = Project::find($post_data['project_id']);
            if (empty($project)) {
                throw ValidationException::withMessages(['project_id' => 'Project is not found']);
            }

            // Check that user is able to make changes in this project.
            $this->authorize('update', $project);

            // Create situation.
            $situation = new Situation([
                'name' => $post_data['name'],
                'description' => $post_data['description'] ?? '',
                'status' => $post_data['status'],
            ]);
            $situation->project()->associate($project);
            $situation->save();

            return response()->json(['id' => $situation->id]);
        });
    }

    /**
     * Update situation.
     *
     * @param Request $request
     * @param int $id
     *
     * @return JsonResponse
     */
    public function patch(Request $request, int $id): JsonResponse {
        return $this->tryCatch(function() use ($request, $id) {
            $situation = Situation::findOrFail($id);
            $this->authorize('update', $situation);
            $post_data = $request->validate([
                'name' => 'string',
                'description' => 'string',
                'status' => 'int',
            ]);
            if (!empty($post_data['name'])) {
                $situation->name = $post_data['name'];
            }
            if (isset($post_data['description'])) {
                $situation->description = $post_data['description'];
            }
            if (isset($post_data['status'])) {
                if (Situation::isStatusAllowed($post_data['status'])) {
                    $situation->status = $post_data['status'];
                }
                else {
                    throw ValidationException::withMessages(['status' => 'Status is not allowed']);
                }
            }
            $situation->save();

            return response()->json(['id' => $situation->id]);
        });
    }

    /**
     * Delete situation.
     *
     * @param int $id
     *
     * @return JsonResponse
     */
    public function delete(int $id): JsonResponse
    {
        return $this->tryCatch(function() use ($id) {
            $situation = Situation::findOrFail($id);
            $this->authorize('delete', $situation);
            Situation::destroy($id);
            return response()->json();
        });
    }

    /**
     * Get the situation.
     *
     * @param int $id
     *
     * @return JsonResponse
     */
    public function get(int $id): JsonResponse {
        return $this->tryCatch(function() use ($id) {
            $situation = Situation::findOrFail($id);
            $this->authorize('view', $situation);
            return response()->json($situation->toArray());
        });
    }

    /**
     * Get all situations of the project.
     *
     * @param int $project_id
     *
     * @return JsonResponse
     */
    public function getAll(int $project_id): JsonResponse
    {
        return $this->tryCatch(function() use ($project_id) {
            $this->authorize('viewAny', Situation::class);

            // Validate project.
            $project = Project::find($project_id);
            if (empty($project)) {
                throw ValidationException::withMessages(['project_id' => 'Project is not found']);
            }

            // Check that user is able to view the project.
            $this->authorize('view', $project);

            $result = Situation::where('project_id', $project->id)->get();

            return response()->json($result);
        });
    }

}
