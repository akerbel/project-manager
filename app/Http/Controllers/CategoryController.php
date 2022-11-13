<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi as OA;

class CategoryController extends Controller
{
    use TryCatchTrait;

    /**
     * Get model name of the controller.
     *
     * @return string
     */
     protected function getModelName(): string
     {
         return 'category';
     }

    /**
     * Create new category.
     *
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @OA\Post(
     *      path="/category",
     *      tags={"Categories"},
     *      summary="Create category",
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
     *          description="Category name",
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *     @OA\Parameter(
     *          name="description",
     *          description="Category description",
     *          in="query",
     *          @OA\Schema(
     *              type="string"
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
            $this->authorize('create', Category::class);
            $post_data = $request->validate([
                'name' => 'required|string',
                'description' => 'string',
            ]);
            $category = new Category([
                'name' => $post_data['name'],
                'description' => $post_data['description'] ?? '',
            ]);
            $category->save();
            return response()->json([
                'id' => $category->id,
            ]);
        });
    }

    /**
     * Update category.
     *
     * @param Request $request
     * @param int $id
     *
     * @return JsonResponse
     *
     * @OA\Put(
     *      path="/category/{id}",
     *      tags={"Categories"},
     *      summary="Completly replace old category",
     *      @OA\Parameter(
     *          name="id",
     *          description="Category id",
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
     *          required=true,
     *          description="Category name",
     *          in="query",
     *          @OA\Schema(
     *              type="string"
     *          )
     *      ),
     *      @OA\Parameter(
     *          name="description",
     *          description="Category description",
     *          in="query",
     *          @OA\Schema(
     *              type="string"
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
     *      )
     *    )
     */
    public function put(Request $request, int $id): JsonResponse
    {
        return $this->tryCatch(function() use ($request, $id) {
            $post_data = $request->validate([
                'name' => 'required|string',
                'description' => 'string',
            ]);
            $category = Category::findOrFail($id);
            $this->authorize('update', $category);
            $category->name = $post_data['name'];
            $category->description = $post_data['description'] ?? '';
            $category->save();
            return response()->json();
        });
    }

    /**
     * Delete category.
     *
     * @param int $id
     *
     * @return JsonResponse
     *
     * @OA\Delete(
     *      path="/category/{id}",
     *      tags={"Categories"},
     *      summary="Delete category",
     *      @OA\Parameter(
     *          name="id",
     *          description="Category id",
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
     *      )
     *    )
     */
    public function delete(int $id): JsonResponse
    {
        return $this->tryCatch(function() use ($id) {
            $category = Category::findOrFail($id);
            $this->authorize('delete', $category);
            Category::destroy($id);
            return response()->json();
        });
    }

    /**
     * Get category.
     *
     * @param int $id
     *
     * @return JsonResponse
     *
     * @OA\Get(
     *      path="/category/{id}",
     *      tags={"Categories"},
     *      summary="Get category",
     *      @OA\Parameter(
     *          name="id",
     *          description="Category id",
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
     *          @OA\JsonContent(ref="#/components/schemas/Category")
     *       ),
     *      @OA\Response(
     *          response=403,
     *          description="Forbidden"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Resource Not Found"
     *      )
     *    )
     */
    public function get(int $id): JsonResponse
    {
        return $this->tryCatch(function() use ($id) {
            $category = Category::findOrFail($id);
            $this->authorize('view', $category);
            return response()->json($category->toArray());
        });
    }

    /**
     * Get all categories.
     *
     * @return JsonResponse
     *
     * @OA\Get(
     *      path="/categories",
     *      tags={"Categories"},
     *      summary="Get all categories",
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
            $this->authorize('viewAny', Category::class);
            $categories = Category::all();
            return response()->json($categories->toArray());
        });
    }

}
