<?php

namespace App\Repository\Mortality;

use App\Models\Mortality;
use App\Repository\Base\BaseRepository;

class MortalityRepository extends BaseRepository implements MortalityRepositoryInterface
{
    protected $model;

    public function __construct(Mortality $model)
    {
        $this->model = $model;
    }
}