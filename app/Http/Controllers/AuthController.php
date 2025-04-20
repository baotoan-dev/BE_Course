<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class AuthController extends Controller
{
    // Đăng nhập và tạo Access Token
    public function login(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
    
        // Extract credentials from the request
        $credentials = $request->only('email', 'password');
    
        // Attempt to generate the JWT access token
        if ($token = JWTAuth::attempt($credentials)) {
            // Create a refresh token with a longer expiry time
            $refreshToken = JWTAuth::fromUser(auth()->user(), ['type' => 'refresh', 'exp' => now()->addDays(7)->timestamp]);
    
            // Respond with the access token and refresh token
            return response()->json([
                'statusCode' => 200,
                'data' => [
                'access_token' => $token,
                'refresh_token' => $refreshToken,
                'token_type' => 'Bearer',
                'expires_in' => config('jwt.ttl') * 60,
                ]
            ]);
        }
    
        // If authentication fails, return an error
        return response()->json(['error' => 'Invalid credentials'], 401);
    }
    

    // Đăng ký
    public function register(Request $request)
    {
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);

        // check email exists

        if (User::where('email', $user->email)->first()) {
            return response()->json(['error' => 'Email already exists', 'key' => 'EMAIL_EXISTS'], 400);
        }

        if ($user->save()) {
            return response()->json(['message' => 'User created successfully']);
        } else {
            return response()->json(['error' => 'Could not create user'], 500);
        }

    }

    // detail user --> id
    public function info(Request $request)
    {
        // validate 
        $request->validate([
            'id' => 'required|integer',
        ]);

        $id = $request->id;

        $user = User::find($id);
        if ($user instanceof User) {
            return response()->json(['statusCode' => 200, 'result' => $user]);
        } else {
            return response()->json(['statusCode' => 404, 'message' => 'User not found'], 404);
        }
    }
    
    // update
    public function update(Request $request)
    {
        $user = Auth::user();
        $user->name = $request->name;
        $user->email = $request->email;

        if ($user instanceof User) {
            $user->save();
            return response()->json(['message' => 'User updated successfully']);
        } else {
            return response()->json(['error' => 'Could not update user'], 500);
        }
    }

    // Update avatar
    public function updateAvatar(Request $request)
    {
        $user = Auth::user();
        if ($user instanceof User) {
            $user->avatar = $request->avatar;
            $user->save();
            return response()->json(['message' => 'Avatar updated successfully', 'statusCode' => 200]);
        } else {
            return response()->json(['error' => 'Could not update avatar', 'statusCode' => 500]);
        }
    }

    // Update password
    public function updatePassword(Request $request)
    {
        // check password compare
        $user = Auth::user();
        if ($user instanceof User) {
            if (Hash::check($request->old_password, $user->password)) {
                $user->password = bcrypt($request->new_password);
                $user->save();
                return response()->json(['message' => 'Password updated successfully']);
            } else {
                return response()->json(['error' => 'Old password is incorrect'], 400);
            }
        } else {
            return response()->json(['error' => 'Could not update password'], 500);
        }
    }


    // delete
    public function delete()
    {
        $user = Auth::user();
        if ($user instanceof User) {
            $user->delete();
            return response()->json(['message' => 'User deleted successfully']);
        } else {
            return response()->json(['error' => 'Could not delete user'], 500);
        }
    }

    public function refreshToken()
    {
        try {
            // Làm mới token và lấy token mới
            $newAccessToken = JWTAuth::parseToken()->refresh();
            // Lấy người dùng hiện tại
            $user = JWTAuth::setToken($newAccessToken)->toUser();
            // Tạo refresh token mới
            $newRefreshToken = JWTAuth::fromUser($user);

            return response()->json([
                'access_token' => $newAccessToken,
                'refresh_token' => $newRefreshToken,
                'token_type' => 'Bearer',
                'expires_in' => config('jwt.ttl') * 60,
            ]);
        } catch (JWTException $e) {
            // Ghi log lỗi để debug
            Log::error('JWT Exception: ' . $e->getMessage());
            return response()->json(['error' => 'Không thể làm mới token', 'message' => $e->getMessage(), 'statusCode' => 401], 401);
        }
    }
    
    // Đăng xuất
    public function logout()
    {
        try {
            $token = JWTAuth::getToken();
            JWTAuth::invalidate($token);
            return response()->json(['message' => 'Successfully logged out']);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Failed to logout'], 500);
        }
    }
    


    // Trả về token và thông tin user
    protected function respondWithToken($token, $refreshToken)
    {
        return response()->json([
            'statusCode' => 200, 
            'data' => [
                'access_token' => $token,
                'refresh_token' => $refreshToken,
                'tokenType' => 'bearer',
                'expiresIn' => config('jwt.ttl') * 60, 
                ]
        ], 200);  
    }
}
