<?php 

namespace App\Services\Report;

use App\Models\Egg;
use App\Models\EggSale;
use App\Models\Expense;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReportService implements ReportServiceInterface
{

    // private $ReportRepository;

    // public function __construct(ReportRepositoryInterface $ReportRepository)
    // {
    //     $this->ReportRepository = $ReportRepository;
    // }

    public function summary()
    {
        $data = [];

        $total_revenue = DB::table('egg_sales')->where('deleted_at',  null)->sum('total_amount');
        $total_expenses = DB::table('expenses')->where('deleted_at',  null)->sum('amount');
        $eggs_produced = DB::table('eggs')
        ->where('deleted_at',  null)
            ->selectRaw("
                SUM(
                    CASE
                        WHEN unit = 'tray' THEN total * 30
                        WHEN unit = 'piece' THEN 1
                        WHEN unit = 'custom' THEN total
                        ELSE total
                    END
                ) as eggs_produced
            ")
            ->value('eggs_produced');

        $eggs_sold = DB::table('egg_sales')->where('deleted_at',  null)
            ->selectRaw("
                SUM(
                    CASE
                        WHEN unit = 'tray' THEN quantity * 30
                        ELSE quantity
                    END
                ) as eggs_sold
            ")
            ->value('eggs_sold');

            $data = [
                'total_revenue' => $total_revenue,
                'total_expenses' => $total_expenses,
                'eggs_produced' => $eggs_produced,
                'eggs_sold' => $eggs_sold,
            ];

        return $data;
    }

    public function generated()
    {

        $reports = DB::table('egg_sales as es')
            ->where('es.deleted_at',  null)
            // Monthly Expenses
            ->leftJoinSub(
                DB::table('expenses')
                ->where('deleted_at',  null)
                    ->selectRaw("
                        YEAR(expense_date) as year,
                        MONTH(expense_date) as month,
                        SUM(amount) as expenses
                    ")
                    ->groupByRaw("YEAR(expense_date), MONTH(expense_date)"),
                'exp',
                function ($join) {
                    $join->on(DB::raw('YEAR(es.sold_at)'), '=', 'exp.year')
                        ->on(DB::raw('MONTH(es.sold_at)'), '=', 'exp.month');
                }
            )

            // Monthly Egg Production
            ->leftJoinSub(
                DB::table('eggs')
                ->where('deleted_at',  null)
                    ->selectRaw("
                        YEAR(date_collected) as year,
                        MONTH(date_collected) as month,
                        SUM(
                            CASE
                                WHEN unit = 'tray' THEN total * 30
                                WHEN unit = 'piece' THEN 1
                                WHEN unit = 'custom' THEN total
                                ELSE total
                            END
                        ) as eggs_produced
                    ")
                    ->groupByRaw("YEAR(date_collected), MONTH(date_collected)"),
                'egg',
                function ($join) {
                    $join->on(DB::raw('YEAR(es.sold_at)'), '=', 'egg.year')
                        ->on(DB::raw('MONTH(es.sold_at)'), '=', 'egg.month');
                }
            )

            ->selectRaw("
                DATE_FORMAT(MIN(es.sold_at), '%b %Y') as period,

                COALESCE(MAX(egg.eggs_produced),0) as eggs_produced,

                SUM(
                    CASE
                        WHEN es.unit = 'tray' THEN es.quantity * 30
                        ELSE es.quantity
                    END
                ) as eggs_sold,

                SUM(es.total_amount) as revenue,

                COALESCE(MAX(exp.expenses),0) as expenses,

                SUM(es.total_amount) - COALESCE(MAX(exp.expenses),0) as net_income
            ")

            ->groupByRaw("YEAR(es.sold_at), MONTH(es.sold_at)")
            ->orderByRaw("YEAR(es.sold_at), MONTH(es.sold_at)")
            ->get();

        return $reports;
      
    }

    public function generatedDetails($period)
    {

        $data = [];
        $date = Carbon::createFromFormat('M Y', $period);

        $eggs = Egg::with('batch')
            ->whereYear('date_collected', $date->year)
            ->whereMonth('date_collected', $date->month)
            ->get();

        $egg_sales = EggSale::with('batch')
            ->select('id', 'sold_at', 'sold_to', 'quantity', 'unit', 'grade', 'price_per_unit', 'total_amount', 'payment_status')
            ->whereYear('sold_at', $date->year)
            ->whereMonth('sold_at', $date->month)
            ->get();

        $expenses = Expense::with('category')
            ->select('id', 'expense_category_id', 'expense_date', 'description', 'amount', 'reference_no')
            ->whereYear('expense_date', $date->year)
            ->whereMonth('expense_date', $date->month)
            ->get();

        $data = [
                'production_summary' => [
                    'total_eggs_produced' => $eggs->sum(function ($egg) {
                        return match ($egg->unit) {
                            'tray' => $egg->total * 30,
                            'piece' => 1,
                            'custom' => $egg->total,
                            default => $egg->total,
                        };
                    }),
                    'total_eggs_sold' => $egg_sales->sum(function ($sale) {
                        return match ($sale->unit) {
                            'tray' => $sale->quantity * 30,
                            default => $sale->quantity,
                        };
                    }),
                
                ],
                'revenue_list' => $egg_sales,
                'revenue_summary' => [
                    'total_paid' => $egg_sales->where('payment_status', 'paid')->sum('total_amount'),
                    'total_unpaid' => $egg_sales->where('payment_status', 'unpaid')->sum('total_amount'),
                    'total_revenue' => $egg_sales->sum('total_amount'),
                    ],
                    'expense_list' => $expenses,
                    'expense_summary' => [
                        'total_expenses' => $expenses->sum('amount'),
                        'by_category' => $expenses
                            ->groupBy('expense_category_id')
                            ->map(function ($group) {
                                return [
                                    'category' => $group->first()->category?->name,
                                    'total_amount' => $group->sum('amount'),
                                ];
                            })
                            ->values(),
                    ],
                    'profit_summary' => [
                        'total_profit' => $egg_sales->sum('total_amount') - $expenses->sum('amount'),
                        'profit_margin' => $egg_sales->sum('total_amount') > 0 ? ($egg_sales->sum('total_amount') - $expenses->sum('amount')) / $egg_sales->sum('total_amount') * 100 : 0,
                        // 'production_rate' => $eggs->count() > 0 ? ($egg_sales->count() / $eggs->count()) * 100 : 0,
                    ]
            ];

        return $data;
    }

}
