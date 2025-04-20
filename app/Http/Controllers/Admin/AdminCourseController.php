<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Http\Controllers\ImageController;

class AdminCourseController extends Controller
{
    public function store(Request $request)
    {

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'author' => 'nullable|string',
            'thumbnail' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'category_ids' => 'required|array',
            // 'category_ids.*' => 'integer|exists:categories,id',
        ]);
        
        if ($request->hasFile('thumbnail')) {
            $newRequest = new Request();
            $newRequest->files->set('image', $request->file('thumbnail'));
        
            $imageController = new ImageController();
            $image = $imageController->uploadGetURL($newRequest);

            if ($image) {
                $validated['thumbnail'] = $image;
            }
        }

        try {
            $course = Course::create([
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'thumbnail' => $validated['thumbnail'] ?? null,
                'author' => $validated['author'] ?? null,
            ]);

            $category_ids = json_decode($validated['category_ids'][0], true);

            $course->categories()->attach($category_ids);
                      
            return response()->json([
                'key' => 'success',
                'message' => 'Course created successfully',
                'data' => $course->load('categories'),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'key' => 'error',
                'message' => 'Could not create course',
                'error' => $e->getMessage(),
            ], 500);
        }
    }   
    
    // Update
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'author' => 'nullable|string',
            'thumbnail' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'category_ids' => 'required|array',
        ]);

        try {
            $course = Course::findOrFail($id);

            if ($request->hasFile('thumbnail')) {
                $newRequest = new Request();
                $newRequest->files->set('image', $request->file('thumbnail'));
            
                $imageController = new ImageController();
                $image = $imageController->uploadGetURL($newRequest);
    
                if ($image) {
                    $validated['thumbnail'] = $image;
                }
            }

            $course->update([
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'thumbnail' => $validated['thumbnail'] ?? null,
                'author' => $validated['author'] ?? null,
            ]);

            // Xóa các category cũ và thêm các category mới
            $category_ids = json_decode($validated['category_ids'][0], true);
            $course->categories()->sync($category_ids);

            return response()->json([
                'key' => 'success',
                'message' => 'Course updated successfully',
                'data' => $course->load('categories'),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'key' => 'error',
                'message' => 'Could not update course',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

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

    // delete course
    public function delete(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
        ]);
    
        $id = $request->id;
    
        $course = Course::find($id);
    
        if ($course) {
            $course->categories()->detach();
            $course->delete();
    
            return response()->json([
                'key' => 'success',
                'message' => 'Course deleted successfully',
            ], 200);
        } else {
            return response()->json([
                'key' => 'error',
                'message' => 'Course not found',
            ], 404);
        }
    }
}
