<?php

namespace App\Repository\ExpenseCategory;

use App\Models\ExpenseCategory;
use App\Repository\Base\BaseRepository;

class ExpenseCategoryRepository extends BaseRepository implements ExpenseCategoryRepositoryInterface
{
    protected $model;

    public function __construct(ExpenseCategory $model)
    {
        $this->model = $model;
    }

    public function Options()
    {
        return $this->model::all();
    }
}