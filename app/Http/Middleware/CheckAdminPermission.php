<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Admin;

class CheckAdminPermission
{
    public function handle(Request $request, Closure $next, $permission)
    {
        $admin = auth()->user(); // Lấy thông tin admin từ token (JWT)

        // Kiểm tra nếu admin không đăng nhập
        if (!$admin) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Lấy vai trò đầu tiên của admin (vì chỉ có một vai trò)
        $role = $admin->roles->first();

        if (!$role) {
            return response()->json(['message' => 'Role not found'], 404);
        }

        // Kiểm tra nếu vai trò của admin là vai trò admin
        if ($role->is_admin) {
            // Nếu là admin, cho phép tiếp tục mà không kiểm tra quyền
            return $next($request);
        }

        // Kiểm tra xem quyền yêu cầu có trong vai trò của admin không
        $hasPermission = $role->adminPermissions->pluck('name')->contains($permission);

        if (!$hasPermission) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return $next($request);
    }
}
