<?php

namespace App\Repository\Egg;

use App\Models\Egg;
use App\Repository\Base\BaseRepository;

class EggRepository extends BaseRepository implements EggRepositoryInterface
{
    protected $model;

    public function __construct(Egg $model)
    {
        $this->model = $model;
    }
}