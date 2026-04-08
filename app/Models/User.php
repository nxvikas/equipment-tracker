<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $fillable = [
        'surname',
        'name',
        'patronymic',
        'email',
        'password',
        'phone',
        'avatar',
        'position_id',
        'department_id',
        'role_id'
    ];

    public function role()
    {
        return $this->belongsTo(\App\Models\Role::class);
    }

    public function position()
    {
        return $this->belongsTo(\App\Models\Position::class);
    }

    public function department()
    {
        return $this->belongsTo(\App\Models\Department::class);
    }

    public function equipment()
    {
        return $this->hasMany(\App\Models\Equipment::class, 'current_user_id');
    }

    public function equipmentHistory()
    {
        return $this->hasMany(\App\Models\Equipment_history::class);
    }

    public function equipmentHistoryTo()
    {
        return $this->hasMany(\App\Models\Equipment_history::class, 'to_user_id');
    }

    public function equipmentHistoryFrom()
    {
        return $this->hasMany(\App\Models\Equipment_history::class, 'from_user_id');
    }

    public function notifications()
    {
        return $this->hasMany(\App\Models\Notification::class);
    }

    public function isAdmin()
    {
        return $this->role->name === 'admin';
    }
    public function isEmployee()
    {
        return $this->role->name === 'employee';
    }

}
