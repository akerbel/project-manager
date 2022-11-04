<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
        $category = new Category([
            'name' => $request->post('name'),
            'description' => $request->post('description', ''),
        ]);
        $category->save();
        return response()->json([
            'id' => $category->id,
        ]);
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
        $category = Category::findOrFail($id);
        $category->name = $request->input('name');
        $category->description = $request->input('description', '');
        $category->save();
        return response()->json();
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
        Category::destroy($id);
        return response()->json();
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
        $category = Category::findOrFail($id);
        return response()->json($category->toArray());
    }

    /**
     * Get all categories.
     *
     * @return JsonResponse
     */
    public function getAll(): JsonResponse
    {
        $categories = Category::all();
        return response()->json($categories->toArray());
    }

}
