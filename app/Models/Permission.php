<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'status',
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'permission_role', 'permission_id', 'role_id');
    }
}
