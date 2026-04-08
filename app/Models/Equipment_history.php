<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Equipment_history extends Model
{
    protected $fillable = [
        'equipment_id',
        'action_type',
        'user_id',
        'from_user_id',
        'to_user_id',
        'from_location_id',
        'to_location_id',
        'old_status',
        'new_status',
        'comment'
    ];


    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function fromUser()
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function toUser()
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }


    public function fromLocation()
    {
        return $this->belongsTo(Location::class, 'from_location_id');
    }

    public function toLocation()
    {
        return $this->belongsTo(Location::class, 'to_location_id');
    }
}
