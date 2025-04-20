<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminPermission extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'status'];

    public function roles()
    {
        return $this->belongsToMany(AdminRole::class, 'admin_permission_role', 'admin_permission_id', 'admin_role_id');
    }
}
