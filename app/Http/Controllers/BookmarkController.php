<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Bookmark;

class BookmarkController extends Controller
{
    public function createBookmark(Request $request)
    {
        $request->validate([
            'course_id' => 'required|integer',
            'status' => 'required|string|in:active,inactive',
        ]);

        try {
            $userId = Auth::id();

            if (!$userId) {
                return response()->json([
                    'statusCode' => 401,
                    'message' => 'Unauthorized',
                ]);
            }

            // check course exists
            $course = Course::where('id', $request->course_id)->first();

            if (!$course) {
                return response()->json([
                    'statusCode' => 404,
                    'message' => 'Course not found',
                ]);
            }

            $bookmark = new Bookmark();
            $bookmark->user_id = $userId;
            $bookmark->course_id = $request->course_id;
            $bookmark->status = $request->status;
            $bookmark->save();

            return response()->json([
                'statusCode' => 200,
                'message' => 'Bookmark created successfully',
                'data' => [
                    'id' => $bookmark->id,
                    'user_id' => $bookmark->user_id,
                    'course_id' => $bookmark->course_id,
                    'status' => $bookmark->status,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'statusCode' => 500,
                'message' => 'Bookmark creation failed',
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function deleteBookmark(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
        ]);

        try {
            $userId = Auth::id();

            if (!$userId) {
                return response()->json([
                    'statusCode' => 401,
                    'message' => 'Unauthorized',
                ]);
            }

            $bookmark = Bookmark::where('id', $request->id)
                ->where('user_id', $userId)
                ->first();

            if (!$bookmark) {
                return response()->json([
                    'statusCode' => 404,
                    'message' => 'Bookmark not found',
                ]);
            }

            $bookmark->delete();

            return response()->json([
                'statusCode' => 200,
                'message' => 'Bookmark deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'statusCode' => 500,
                'message' => 'Bookmark deletion failed',
                'error' => $e->getMessage(),
            ]);
        }
    }
}

