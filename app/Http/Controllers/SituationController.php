<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Situation;
use Illuminate\Database\Eloquent\ModelNotFoundException;
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
     *
     * @OA\Post(
     *      path="/situation",
     *      tags={"Situations"},
     *      summary="Create situation",
     *      @OA\Parameter(
     *          name="Authorization",
     *          description="Authorization token",
     *          required=true,
     *          in="header",
     *          @OA\Schema(
     *              type="string",
     *              example="Bearer 1|y6VIcnEFVMpQvOHStKU0dcXD8CKEdJ0nQuW3anF6"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="project_id",
     *          description="Project id",
     *          required=true,
     *          in="query",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="name",
     *          description="Situation name",
     *          in="query",
     *          required=true,
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="description",
     *          description="Situation description",
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="status",
     *          description="Situation status",
     *          in="query",
     *          required=true,
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      )
     *    )
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
     *
     * @OA\Patch(
     *      path="/situation/{id}",
     *      tags={"Situations"},
     *      summary="Update situation details",
     *      @OA\Parameter(
     *          name="id",
     *          description="Situation id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="Authorization",
     *          description="Authorization token",
     *          required=true,
     *          in="header",
     *          @OA\Schema(
     *              type="string",
     *              example="Bearer 1|y6VIcnEFVMpQvOHStKU0dcXD8CKEdJ0nQuW3anF6"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="name",
     *          description="Situation name",
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="description",
     *          description="Situation description",
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="status",
     *          description="Situation status",
     *          in="query",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *       ),
     *      @OA\Response(
     *          response=400,
     *          description="Bad Request",
     *      ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Resource Not Found"
     *      ),
     *    )
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
     *
     * @OA\Delete(
     *      path="/situation/{id}",
     *      tags={"Situations"},
     *      summary="Delete situation",
     *      @OA\Parameter(
     *          name="id",
     *          description="Situation id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="Authorization",
     *          description="Authorization token",
     *          required=true,
     *          in="header",
     *          @OA\Schema(
     *              type="string",
     *              example="Bearer 1|y6VIcnEFVMpQvOHStKU0dcXD8CKEdJ0nQuW3anF6"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *       ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Resource Not Found"
     *      ),
     *    )
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
     *
     * @OA\Get(
     *      path="/situation/{id}",
     *      tags={"Situations"},
     *      summary="Get situation details",
     *      @OA\Parameter(
     *          name="id",
     *          description="Situation id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="Authorization",
     *          description="Authorization token",
     *          required=true,
     *          in="header",
     *          @OA\Schema(
     *              type="string",
     *              example="Bearer 1|y6VIcnEFVMpQvOHStKU0dcXD8CKEdJ0nQuW3anF6"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(ref="#/components/schemas/Situation")
     *       ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Resource Not Found"
     *      ),
     *    )
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
     *
     * @OA\Get(
     *      path="/situations/{project_id}",
     *      tags={"Situations"},
     *      summary="Get all situations of the project",
     *      @OA\Parameter(
     *          name="project_id",
     *          description="Project id",
     *          required=true,
     *          in="path",
     *          @OA\Schema(
     *              type="integer"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="Authorization",
     *          description="Authorization token",
     *          required=true,
     *          in="header",
     *          @OA\Schema(
     *              type="string",
     *              example="Bearer 1|y6VIcnEFVMpQvOHStKU0dcXD8CKEdJ0nQuW3anF6"
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *       ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *    )
     */
    public function getAll(int $project_id): JsonResponse
    {
        return $this->tryCatch(function() use ($project_id) {
            $this->authorize('viewAny', Situation::class);

            // Validate project.
            $project = Project::find($project_id);
            if (empty($project)) {
                throw new ModelNotFoundException('Project is not found');
            }

            // Check that user is able to view the project.
            $this->authorize('view', $project);

            $result = Situation::where('project_id', $project->id)->get();

            return response()->json($result);
        });
    }

}
