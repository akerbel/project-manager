<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OpenApi as OA;

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
     *
     * @OA\Post(
     *      path="/project",
     *      tags={"Projects"},
     *      summary="Create project",
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
     *          required=true,
     *          description="Project name",
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *     @OA\Parameter(
     *          name="description",
     *          description="Project description",
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="categories",
     *          required=true,
     *          description="Array of categories",
     *          in="query",
     *          @OA\Schema(
     *              type="integer[]"
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
     *
     * @OA\Patch(
     *      path="/project/{id}",
     *      tags={"Projects"},
     *      summary="Update project details",
     *      @OA\Parameter(
     *          name="id",
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
     *      @OA\Parameter(
     *          name="name",
     *          description="Project name",
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="description",
     *          description="Project description",
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="categories",
     *          description="Array of categories",
     *          in="query",
     *          @OA\Schema(
     *              type="integer[]"
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
     *
     * @OA\Delete(
     *      path="/project/{id}",
     *      tags={"Projects"},
     *      summary="Delete project",
     *      @OA\Parameter(
     *          name="id",
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
     *      @OA\Response(
     *          response=404,
     *          description="Resource Not Found"
     *      ),
     *    )
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
     *
     * @OA\Get(
     *      path="/project/{id}",
     *      tags={"Projects"},
     *      summary="Get project details",
     *      @OA\Parameter(
     *          name="id",
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
     *          @OA\JsonContent(ref="#/components/schemas/Project")
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
     *
     * @OA\Get(
     *      path="/projects",
     *      tags={"Projects"},
     *      summary="Get all projects",
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
     *      )
     *    )
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
