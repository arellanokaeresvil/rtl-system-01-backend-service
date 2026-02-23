<?php

namespace App\Repository\Sale;

use App\Models\BirdSale;
use App\Repository\Base\BaseRepository;

class BirdSaleRepository extends BaseRepository implements BirdSaleRepositoryInterface
{
    protected $model;

    public function __construct(BirdSale $model)
    {
        $this->model = $model;
    }
}