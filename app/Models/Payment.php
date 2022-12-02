<?php

namespace App\Models;

use App\traits\UseUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory,UseUuid;

    protected $guarded = [];

    protected $with = ['model', 'order'];

    /** relations */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function model()
    {
        return $this->morphOne(Status::class, 'model');
    }
}
