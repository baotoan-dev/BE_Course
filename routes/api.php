<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\BookmarkController;
// Admin
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminRoleController;
use App\Http\Controllers\Admin\AdminCategoryController; 
use App\Http\Controllers\Admin\AdminCourseController;
/*
|--------------------------------------------------------------------------- 
| API Routes 
|--------------------------------------------------------------------------- 
| 
| Here is where you can register API routes for your application. 
| These routes are loaded by the RouteServiceProvider within a group which 
| is assigned the "api" middleware group. Enjoy building your API! 
|
*/
// Admin
Route::prefix('auth')->group(function () {
    Route::post('/admin/login', [AdminAuthController::class, 'adminLogin']);  // Admin login
    Route::post('/login', [AuthController::class, 'login']);  // User login
});

Route::post('/admin/register', [AdminUserController::class, 'createAdmin']);  // Admin register

Route::prefix('admin')->middleware(['auth:admin'])->group(function () {
        // Auth
    Route::get('/info', [AdminAuthController::class, 'adminInfo']);  // Admin info
    Route::post('/refresh-token', [AdminAuthController::class, 'adminRefresh']);  // Admin refresh token
    Route::post('/logout', [AdminAuthController::class, 'adminLogout']);  // Admin logout
    Route::post('/update', [AdminUserController::class, 'updateAdmin']);  // Admin update

    Route::middleware(['check.permission:manage-users'])->group(function () {
        // User
        Route::get('/users', [AdminUserController::class, 'listAdmins']);  // Get all users
        Route::get('/users/{id}', [AdminUserController::class, 'getById']);  // Get user by id
        Route::post('/users', [AdminUserController::class, 'createAdmin']);  // Create user
        Route::put('/users/{id}', [AdminUserController::class, 'updateAdmin']);  // Update user
        Route::delete('/users/{id}', [AdminUserController::class, 'deleteAdmin']);  // Delete user
    });
    Route::middleware(['check.permission:manage-categories'])->group(function () {
        // Category
        Route::get('/categories', [AdminCategoryController::class, 'getCategories']);  // Get all categories
        Route::get('/categories/{id}', [AdminCategoryController::class, 'detail']);  // Get category by id
        Route::post('/categories', [AdminCategoryController::class, 'store']);  // Create category
        Route::put('/categories/{id}', [AdminCategoryController::class, 'update']);  // Update category
        Route::delete('/categories/{id}', [AdminCategoryController::class, 'delete']);  // Delete category
    });
    // Role
    Route::middleware(['check.permission:manage-roles'])->group(function () {
        Route::get('/roles/{id}', [AdminRoleController::class, 'getById']);  // Get role by id
        Route::put('/roles/{id}', [AdminRoleController::class, 'updateRole']);  // Update role
        Route::delete('/roles/{id}', [AdminRoleController::class, 'deleteRole']);  // Delete role
        Route::get('/roles', [AdminRoleController::class, 'getRoles']);  // Get all roles
    });

    // Course
    Route::middleware(['check.permission:manage-courses'])->group(function () {
        Route::get('/courses', [AdminCourseController::class, 'index']);  // Get all courses
        Route::get('/courses/{id}', [AdminCourseController::class, 'detail']);  // Get course by id
        Route::post('/courses', [AdminCourseController::class, 'store']);  // Create course
        Route::put('/courses/{id}', [AdminCourseController::class, 'update']);  // Update course
        Route::delete('/courses/{id}', [AdminCourseController::class, 'delete']);  // Delete course
    });
});

// User
// Không cần xác thực
Route::post('/logout', [AuthController::class, 'logout']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/refresh-token', [AuthController::class, 'refreshToken']);

// Không cần xác thực
// User
Route::get('/user/info', [AuthController::class, 'info']);
// Category
Route::get('/categories/{id}', [CategoryController::class, 'detail']);
Route::get('/categories', [CategoryController::class, 'getCategories']);
Route::delete('/categories/{id}', [CategoryController::class, 'delete']);
// Courses
Route::get('courses/{id}', [CourseController::class, 'detail']);
Route::middleware(['auth:api'])->get('courses', [CourseController::class, 'index']);
// Image
Route::post('/upload', [ImageController::class, 'upload']);
Route::post('/delete', [ImageController::class, 'delete']);
Route::post('/uploads', [ImageController::class, 'uploads']);

// Cần xác thực
Route::middleware(['auth:api'])->group(function () {
    Route::get('/info', function (Request $request) {
        return response()->json(['statusCode' => 200,'result' => $request->user()]);
    });

    // Update avatar
    Route::put('/update-avatar', [AuthController::class, 'updateAvatar']);

    // Category
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::put('/categories/{id}', [CategoryController::class, 'update']);

    // Courses
    Route::post('/courses', [CourseController::class, 'store']);
    Route::put('/courses/{id}', [CourseController::class, 'update']);

    // Bookmark
    Route::post('/bookmarks', [BookmarkController::class, 'createBookmark']);
    Route::delete('/bookmarks/{id}', [BookmarkController::class, 'deleteBookmark']);
});



