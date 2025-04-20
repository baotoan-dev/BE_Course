<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Http\Controllers\ImageController;
use Illuminate\Support\Facades\DB;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        // Nhận tham số từ request
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 10);
        $search = $request->get('search', '');

        // Xây dựng query với tìm kiếm và phân trang
        $query = Course::with('categories');

        if (!empty($search)) {
            $query->where('title', 'like', '%' . $search . '%')
                ->orWhere('description', 'like', '%' . $search . '%');
        }

        $courses = $query->paginate($limit, ['*'], 'page', $page);

        // Chuyển đổi dữ liệu
        $convertedCourses = $courses->items();
        $convertedCourses = array_map(function ($course) {
            return [
                'id' => $course->id,
                'title' => $course->title,
                'description' => $course->description,
                'thumbnail' => $course->thumbnail,
                'author' => $course->author,
                'categories' => $course->categories->pluck('name'),
            ];
        }, $convertedCourses);

        // Trả về response
        return response()->json([
            'key' => 'success',
            'statusCode' => 200,
            'message' => 'Get all courses successfully',
            'data' => [
                'current_page' => $courses->currentPage(),
                'total' => $courses->total(),
                'last_page' => $courses->lastPage(),
                'per_page' => $courses->perPage(),
                'courses' => $convertedCourses,
            ],
        ], 200);
    }

    public function detail(Request $request)
    {
        $id = $request->id;
    
        $course = Course::with('categories')->find($id);
    
        if ($course) {
            return response()->json([
                'key' => 'success',
                'message' => 'Get course detail successfully',
                'data' => [
                    'id' => $course->id,
                    'title' => $course->title,
                    'description' => $course->description,
                    'thumbnail' => $course->thumbnail,
                    'author' => $course->author,
                    'categories' => $course->categories->pluck('name'),
                ],
            ], 200);
        } else {
            return response()->json([
                'key' => 'error',
                'message' => 'Course not found',
            ], 404);
        }
    }
}
