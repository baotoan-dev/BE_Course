<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Http\Services\CategoryService;

class CategoryController extends Controller
{
    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
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
}
