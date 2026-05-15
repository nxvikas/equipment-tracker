<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Http\Enums\UserStatus;


class User extends Authenticatable
{
    use Notifiable;
    protected $fillable = [
        'surname',
        'name',
        'patronymic',
        'email',
        'password',
        'phone',
        'avatar',
        'position_id',
        'role_id',
        'status',
    ];
    protected $casts = [
        'status' => UserStatus::class,
    ];

    public function role()
    {
        return $this->belongsTo(\App\Models\Role::class);
    }

    public function position()
    {
        return $this->belongsTo(\App\Models\Position::class);
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


    public function isAdmin()
    {
        return $this->role->name === 'admin';
    }

    public function isEmployee()
    {
        return $this->role->name === 'employee';
    }

}
