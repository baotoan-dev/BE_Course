<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Log;

class AdminAuthController extends Controller
{
    //
    public function adminLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
    
        $credentials = $request->only('email', 'password');
    
        if ($token = auth('admin')->attempt($credentials)) {
            $refreshToken = JWTAuth::fromUser(auth('admin')->user(), ['type' => 'refresh', 'exp' => now()->addDays(7)->timestamp]);
    
            return response()->json([
                'statusCode' => 200,
                'data' => [
                    'access_token' => $token,
                    'refresh_token' => $refreshToken,
                    'token_type' => 'Bearer',
                    'expires_in' => JWTAuth::factory()->getTTL() * 60, // Fix lỗi factory()
                ]
            ]);
        }
    
        return response()->json(['error' => 'Invalid credentials'], 401);
    }

    // info
    public function adminInfo(Request $request)
    {
        return response()->json([
            'statusCode' => 200,
            'message' => 'Admin info',
            'data' => auth('admin')->user(),
        ]);
    }

    // refresh token
    public function adminRefresh()
    {
       try
       {
        $newAccessToken = JWTAuth::parseToken()->refresh();

        $user = auth('admin')->user();

        $refreshToken = JWTAuth::fromUser($user, ['type' => 'refresh', 'exp' => now()->addDays(7)->timestamp]);

        return response()->json([
            'statusCode' => 200,
            'data' => [
                'access_token' => $newAccessToken,
                'refresh_token' => $refreshToken,
                'token_type' => 'Bearer',
                'expires_in' => JWTAuth::factory()->getTTL() * 60, // Fix lỗi factory()
            ]
        ]);
    } catch (JWTException $e) {
            // Ghi log lỗi để debug
            Log::error('JWT Exception: ' . $e->getMessage());
            return response()->json(['error' => 'Không thể làm mới token', 'message' => $e->getMessage(), 'statusCode' => 401], 401);
        }
    }

    // logout
    public function adminLogout(Request $request)
    {
        auth('admin')->logout();
        return response()->json(['message' => 'Successfully logged out', 'statusCode' => 200]);
    }
}
