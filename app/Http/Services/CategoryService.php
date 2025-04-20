<?php

namespace App\Http\Services;
use App\Models\Category;
use Exception;

class CategoryService
{
    /**
     * Xóa category và các bản ghi liên quan
     *
     * @param int $id
     * @return array
     */
    public function deleteCategory(int $id): array
    {
        $category = Category::find($id);

        if (!$category) {
            return [
                'status' => false,
                'message' => 'Category not found',
                'code' => 404,
            ];
        }

        try {
            $category->courses()->detach();
            $category->delete();

            return [
                'status' => true,
                'message' => 'Category deleted successfully',
                'code' => 200,
            ];
        } catch (Exception $e) {
            return [
                'status' => false,
                'message' => 'Could not delete category',
                'error' => $e->getMessage(),
                'code' => 500,
            ];
        }
    }
}
