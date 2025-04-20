<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminRole extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'status', 'description', 'is_admin']; // Thêm is_admin vào $fillable

    public function admins()
    {
        return $this->belongsToMany(Admin::class, 'admin_role_pivot', 'admin_role_id', 'admin_id');
    }

    public function adminPermissions()
    {
        return $this->belongsToMany(AdminPermission::class, 'admin_permission_role', 'admin_role_id', 'admin_permission_id');
    }
}
