<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Http\Services\CategoryService;

class AdminCategoryController extends Controller
{
    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
        ]);

        try {
            $category = Category::create([
                'name' => $request->name,
                'description' => $request->description,
            ]);

            return response()->json([
                'key' => 'success',
                'message' => 'Category created successfully',
                'data' => $category,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'key' => 'error',
                'message' => 'Could not create category',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getCategories(Request $request)
    {
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 20);
        $search = $request->get('search', '');
    
        $query = Category::query();
    
        if (!empty($search)) {
            $query->where('name', 'like', '%' . $search . '%');
        }
    
        $categories = $query->paginate($limit, ['*'], 'page', $page);
    
        return response()->json([
            'statusCode' => 200,    
            'key' => 'success',
            'data' => $categories,
        ], 200);
    }
    

    public function detail(Request $request, $id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'key' => 'error',
                'message' => 'Category not found',
            ], 404);
        }

        return response()->json([
            'key' => 'success',
            'data' => $category,
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
        ]);

        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'key' => 'error',
                'message' => 'Category not found',
            ], 404);
        }

        try {
            $category->name = $request->name;
            $category->description = $request->description;
            $category->save();

            return response()->json([
                'key' => 'success',
                'message' => 'Category updated successfully',
                'data' => $category,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'key' => 'error',
                'message' => 'Could not update category',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Xóa category và các bản ghi liên quan
     */

    public function delete(Request $request, $id)
    {
        $result = $this->categoryService->deleteCategory($id);

        if ($result['status']) {
            return response()->json([
                'key' => 'success',
                'message' => $result['message'],
            ], 200);
        } else {
            return response()->json([
                'key' => 'error',
                'message' => $result['message'],
                'error' => $result['error'],
            ], $result['code']);
        }
    }
}
