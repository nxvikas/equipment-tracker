<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    protected $fillable = [
        'inventory_number',
        'name',
        'manufacturer',
        'model',
        'serial_number',
        'status',
        'purchase_date',
        'purchase_price',
        'warranty_date',
        'qr_code',
        'notes',
        'status_comment',
        'current_user_id',
        'location_id',
        'category_id'
    ];

    public function currentUser()
    {
        return $this->belongsTo(User::class, 'current_user_id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function history()
    {
        return $this->hasMany(Equipment_history::class);
    }
}
