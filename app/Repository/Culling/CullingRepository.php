<?php

namespace App\Repository\Culling;

use App\Models\Culling;
use App\Repository\Base\BaseRepository;

class CullingRepository extends BaseRepository implements CullingRepositoryInterface
{
    protected $model;

    public function __construct(Culling $model)
    {
        $this->model = $model;
    }
}