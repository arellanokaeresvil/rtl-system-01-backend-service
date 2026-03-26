<?php 

namespace App\Services\Expense;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Repository\Expense\ExpenseRepositoryInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ExpenseService implements ExpenseServiceInterface
{

    private $expenseRepository;

    public function __construct(ExpenseRepositoryInterface $expenseRepository)
    {
        $this->expenseRepository = $expenseRepository;
    }

    public function summary()
    {
        $start = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();

        $data = ExpenseCategory::leftJoin('expenses', 'expense_categories.id', '=', 'expenses.expense_category_id')
            ->whereBetween('expenses.expense_date', [$start, $end])
            ->where('expenses.deleted_at', NULL)
            ->select('expense_categories.name')
            ->selectRaw('COALESCE(SUM(expenses.amount), 0) as total_amount')
            ->groupBy('expense_categories.name')
            ->get();

        $total = $data->sum('total_amount');

        $data = $data->map(function ($item) use ($total) {
            $item->percentage = $total > 0 
                ?round(($item->total_amount / $total) * 100, 2)
                : 0;
            return $item;
        });
        return $data;
    }

}
