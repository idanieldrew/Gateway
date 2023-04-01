<?php

namespace App\Models;

use App\traits\UseUuid;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory, UseUuid;

    protected $guarded = [];

    /** relations */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function model()
    {
        return $this->morphOne(Status::class, 'model');
    }

    public function scopeIsExpired(Builder $query, $time)
    {
        $query->where('expired_at', '>=', $time);
    }
}
