<?php

namespace App\Repository\Status\v1;

use App\Models\Status;
use App\Repository\Repository;
use Illuminate\Database\Eloquent\Model;

class StatusRepository implements Repository
{
    public function model()
    {
        return Status::query();
    }

    /**
     * Update status for model(Model should implement morph relation)
     *
     * @param Model $model
     * @param string $name
     * @param string $reason
     * @return mixed
     */
    public function updateStatus(Model $model, string $name, string $reason)
    {
        return $model->status()->update([
            'name' => $name,
            'reason' => $reason
        ]);
    }
}
