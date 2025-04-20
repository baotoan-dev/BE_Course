<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdminRole;

class AdminRoleController extends Controller
{

    /**
 * @OA\Get(
 *     path="/api/admin/roles",
 *     tags={"Admin"},
 *     summary="Get all roles",
 *     description="Get all roles",
 *     operationId="getRoles",
 *     @OA\Parameter(
 *         name="page",
 *         in="query",
 *         description="Page number",
 *         required=false,
 *         @OA\Schema(
 *             type="integer",
 *             default=1
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="limit",
 *         in="query",
 *         description="Number of items per page",
 *         required=false,
 *         @OA\Schema(
 *             type="integer",
 *             default=10
 *         )
 *     ),
 *     @OA\Parameter(
 *         name="search",
 *         in="query",
 *         description="Search by name",
 *         required=false,
 *         @OA\Schema(
 *             type="string",
 *             default=""
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Success"
 *     )
 * )
 */
    public function createRole(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',  
            'status' => 'required|string|in:active,inactive', // Chỉ chấp nhận active hoặc inactive
            'description' => 'string|max:255', // Mô tả có thể không cần
            'permissions' => 'array', // Danh sách permissions phải là mảng
            'permissions.*' => 'string|max:255' // Mỗi phần tử trong mảng phải là chuỗi
        ]);

        try {
            // Tạo role mới
            $role = new AdminRole();
            $role->name = $request->name;
            $role->description = $request->description ?? '';
            $role->status = $request->status;
            $role->save();
    
            // Nếu có danh sách permissions, tìm hoặc tạo mới
            if ($request->has('permissions') && !empty($request->permissions)) {
                $permissionIds = [];
                
                foreach ($request->permissions as $permissionName) {
                    $permission = \App\Models\AdminPermission::firstOrCreate(
                        ['name' => $permissionName], // Kiểm tra theo tên
                        ['name' => $permissionName]  // Nếu chưa có, tạo mới
                    );
                    $permissionIds[] = $permission->id;
                }
    
                // Gán quyền vào role
                $role->adminPermissions()->sync($permissionIds);
            }
    
            return response()->json([
                'statusCode' => 200,
                'message' => 'Role created successfully',
                'data' => [
                    'id' => $role->id,
                    'name' => $role->name,
                    'description' => $role->description,
                    'status' => $role->status,
                    'permissions' => $role->adminPermissions()->pluck('name'), // Lấy danh sách tên permission
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'statusCode' => 500,
                'message' => 'Internal server error',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function listRoles(Request $request)
    {
        $page = $request->query('page', 1);
        $limit = $request->query('limit', 20);
        $search = $request->query('search', '');
        $status = $request->query('status', '');
        
        $roles = AdminRole::where('name', 'like', "%$search%")
            ->when($status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->orderBy('id', 'desc')
            ->paginate($limit, ['*'], 'page', $page);

        $roles->getCollection()->transform(function ($role) {
            return [
                'id' => $role->id,
                'name' => $role->name,
                'description' => $role->description,
                'status' => $role->status,
                'permissions' => $role->adminPermissions()->pluck('name'), // Lấy danh sách tên permission
            ];
        });
        
        return response()->json([
            'statusCode' => 200,
            'data' => $roles,
        ]);
    }
}