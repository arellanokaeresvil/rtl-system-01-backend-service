<?php

namespace App\Repository\Expense;

use App\Models\Expense;
use App\Repository\Base\BaseRepository;

class ExpenseRepository extends BaseRepository implements ExpenseRepositoryInterface
{
    protected $model;

    public function __construct(Expense $model)
    {
        $this->model = $model;
    }

}