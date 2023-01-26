<?php

namespace App\Models;

use App\traits\UseUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    use HasFactory,UseUuid;

    protected $guarded = [];

    /** relations */
    public function status()
    {
        return $this->morphTo();
    }
}
