<?php

namespace Dietando\Entities;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $table = "items";

    protected $fillable = [
        'meal_id',
        'item',
        'quantity',
        'unity',
        'check',
        'checked_at'
    ];

    protected $dates = ['checked_at'];

    protected $casts = [
        'quantity' => 'float'
    ];

    public function meal()
    {
        return $this->belongsTo(Meal::class, 'meal_id');
    }
}
