<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    protected $fillable = ['name', 'department_id'];

    public function users()
    {
        return $this->hasMany(\App\Models\User::class);
    }
    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
