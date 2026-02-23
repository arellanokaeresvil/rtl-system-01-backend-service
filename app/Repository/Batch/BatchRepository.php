<?php

namespace App\Repository\Batch;

use App\Models\Batch;
use App\Repository\Base\BaseRepository;

class BatchRepository extends BaseRepository implements BatchRepositoryInterface
{
    protected $model;

    public function __construct(Batch $model)
    {
        $this->model = $model;
    }

        public function Options()
    {
        return $this->model->where('current_quantity', '>', 0)->get();
    }
}