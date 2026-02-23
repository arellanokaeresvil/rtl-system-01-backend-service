<?php

namespace App\Repository\Sale;

use App\Models\EggSale;
use App\Repository\Base\BaseRepository;

class EggSaleRepository extends BaseRepository implements EggSaleRepositoryInterface
{
    protected $model;

    public function __construct(EggSale $model)
    {
        $this->model = $model;
    }
}