<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CategoryController extends Controller
{

    /**
     * Create new category.
     *
     * @param Request $request
     *
     * @return JsonResponse
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
     */
    public function getAll(): JsonResponse
    {
        return $this->tryCatch(function() {
            $this->authorize('viewAny', Category::class);
            $categories = Category::all();
            return response()->json($categories->toArray());
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
            return response()->json(['error' => 'Category is not found'], 404);
        }
        catch (AuthorizationException $e) {
            return response()->json(['error' => 'Access is forbidden.'], 403);
        }
        catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

}
