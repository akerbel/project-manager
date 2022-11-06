<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Project;
use App\Models\Situation;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class SituationController extends Controller
{
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
     * @param $id
     *
     * @return JsonResponse
     */
    public function get($id): JsonResponse {
        return $this->tryCatch(function() use ($id) {
            $situation = Situation::findOrFail($id);
            $this->authorize('view', $situation);
            return response()->json($situation->toArray());
        });
    }

    /**
     * Try a code and catch typical exceptions.
     *
     * @param $func
     *
     * @return JsonResponse
     */
    protected function tryCatch($func) {
        try {
            return $func();
        }
        catch (ValidationException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
        catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Situation is not found'], 404);
        }
        catch (AuthorizationException $e) {
            return response()->json(['error' => 'Access is forbidden.'], 403);
        }
        catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
