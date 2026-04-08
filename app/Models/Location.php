<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $fillable=[
        'name',
        'type',
        'address'
    ];

    public function equipment()
    {
        return $this->hasMany(\App\Models\Equipment::class);
    }
    public function historiesFrom(){
        return $this->hasMany(Equipment_history::class,'from_location_id');
    }
    public function historiesTo(){
        return $this->hasMany(Equipment_history::class,'to_location_id');
    }
}
