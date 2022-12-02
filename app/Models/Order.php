<?php

namespace App\Models;

use App\traits\UseUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory,UseUuid;

    protected $with = ['model'];

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
}
