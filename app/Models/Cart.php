<?php

namespace App\Models;

use App\traits\UseUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory, UseUuid;

    protected $guarded = [];

    /** relations */
    public function cart_items()
    {
        return $this->hasMany(CartItem::class);
    }

    public function status()
    {
        return $this->morphOne(Status::class, 'model');
    }

    public function order()
    {
        return $this->hasMany(Order::class);
    }

    /** end relations */
}
