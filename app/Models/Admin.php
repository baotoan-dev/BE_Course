<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;

class Admin extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        // 'password' => 'hashed',
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'admin_role_pivot', 'admin_id', 'admin_role_id');
    }

    public function hasRole($role)
    {
        return $this->roles->pluck('name')->contains($role);
    }

    public function assignRole($role)
    {
        $role = Role::where('name', $role)->first();
        if ($role) {
            $this->roles()->syncWithoutDetaching([$role->id]);
        }
    }

    public function removeRole($role)
    {
        $role = Role::where('name', $role)->first();
        if ($role) {
            $this->roles()->detach($role->id);
        }
    }

    public function hasAnyRole($roles)
    {
        return $this->roles->pluck('name')->intersect($roles)->isNotEmpty();
    }

    public function hasAllRoles($roles)
    {
        return $this->roles->pluck('name')->intersect($roles)->count() == count($roles);
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    // public function setPasswordAttribute($value)
    // {
    //     $this->attributes['password'] = bcrypt($value);
    // }
}
