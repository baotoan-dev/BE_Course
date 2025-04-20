<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use App\Models\AdminRole;

class AdminUserController extends Controller
{
    public function createAdmin(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:admins',
            'password' => 'required',
        ]);

        // check email exists
        $admin = Admin::where('email', $request->email)->first();
        if ($admin instanceof Admin) {
            return response()->json([
                "key" => "EMAIL_EXISTS",
                'statusCode' => 400,
                'message' => 'Email already exists',
            ]);
        }

        $admin = new Admin();
        $admin->name = $request->name;
        $admin->email = $request->email;
        $admin->password = bcrypt($request->password);

        if ($admin->save()) {
            return response()->json([
                'statusCode' => 200,
                'message' => 'Admin created successfully',
            ]);
        }

        return response()->json([
            'statusCode' => 500,
            'message' => 'Internal server error',
        ]);
    }

    public function updateAdmin(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:admins,email,' . $id,
            'role_id' => 'required|exists:roles,id',
        ]);

        // check role id exists
        $role = AdminRole::find($request->role_id);
        if (!$role instanceof AdminRole) {
            return response()->json([
                'statusCode' => 400,
                'message' => 'Role not found',
            ]);
        }

        // save admin pivot role


        $admin = Admin::find($id);
        $admin->name = $request->name;
        $admin->email = $request->email;

        if ($admin->save()) {
            $admin->roles()->sync([$role->id]);

            return response()->json([
                'statusCode' => 200,
                'message' => 'Admin updated successfully',
            ]);
        }

        return response()->json([
            'statusCode' => 500,
            'message' => 'Internal server error',
        ]);
    }

    public function deleteAdmin($id)
    {
        $admin = Admin::find($id);
        if ($admin->delete()) {
            return response()->json([
                'statusCode' => 200,
                'message' => 'Admin deleted successfully',
            ]);
        }

        return response()->json([
            'statusCode' => 500,
            'message' => 'Internal server error',
        ]);
    }

    public function listAdmins(Request $request)
    {
        $page = $request->query('page', 1);   
        $limit = $request->query('limit', 20); 
        $search = $request->query('search', ''); 
    
        $query = Admin::query();
        $query->with('roles');
    
        if (!empty($search)) {
            $query->where('name', 'LIKE', "%$search%")
                  ->orWhere('email', 'LIKE', "%$search%");
        }
    
        $admins = $query->paginate($limit, ['*'], 'page', $page);
    
        return response()->json([
            'statusCode' => 200,
            'data' => $admins,
        ]);
    }

    public function getById($id) {
        $admin = Admin::find($id);
        if ($admin instanceof Admin) {
            return response()->json([
                'statusCode' => 200,
                'data' => $admin,
            ]);
        }

        return response()->json([
            'key' => 'ADMIN_NOT_FOUND', 
            'statusCode' => 404,
            'message' => 'Admin not found',
        ]);
    }
}