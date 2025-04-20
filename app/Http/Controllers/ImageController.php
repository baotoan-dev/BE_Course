<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class ImageController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
    
        try {
            $file = $request->file('image');
    
            $uploadedFileUrl = Cloudinary::upload($file->getRealPath(), [
                'folder' => 'php-uploads',
            ])->getSecurePath();
    
            return response()->json([
                'statusCode' => 200,
                'message' => 'Image uploaded successfully!',
                'result' => $uploadedFileUrl,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'statusCode' => 500,
                'message' => 'Image upload failed!',
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function uploadGetURL(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
    
        try {
            $file = $request->file('image');
    
            $uploadedFileUrl = Cloudinary::upload($file->getRealPath(), [
                'folder' => 'php-uploads',
            ])->getSecurePath();
    
            return $uploadedFileUrl;
        } catch (\Exception $e) {
            return response()->json([
                'statusCode' => 500,
                'message' => 'Image upload failed!',
                'error' => $e->getMessage(),
            ]);
        }
    }

    

    public function delete(Request $request)
    {
        $request->validate([
            'public_id' => 'required|string',
        ]);

        $publicId = $request->public_id;

        Cloudinary::destroy($publicId);

        return response()->json([
            'statusCode' => 200,
            'message' => 'Image deleted successfully!',
        ]);
    }

    public function uploads(Request $request)
    {
        $request->validate([
            'images' => 'required',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $uploadedFileUrls = [];

        foreach ($request->file('images') as $file) {
            $uploadedFileUrl = Cloudinary::upload($file->getRealPath(), [
                'folder' => 'php-uploads',
            ])->getSecurePath();

            $uploadedFileUrls[] = $uploadedFileUrl;
        }

        return response()->json([
            'statusCode' => 200,
            'message' => 'Images uploaded successfully!',
            'result' => $uploadedFileUrls,
        ]);
    }
}
