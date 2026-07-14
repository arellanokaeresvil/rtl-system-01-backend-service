<?php 

namespace App\Services\Sale;

use App\Models\BirdSale;
use App\Models\Egg;
use App\Models\EggSale;
use App\Repository\Batch\BatchRepositoryInterface;
use App\Repository\Sale\BirdSaleRepositoryInterface;
use App\Repository\Sale\EggSaleRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class SaleService implements SaleServiceInterface
{

    private $eggSaleRepository;
    private $birdSaleRepository;
    private $batchRepository;

    public function __construct(EggSaleRepositoryInterface $eggSaleRepository, BirdSaleRepositoryInterface $birdSaleRepository, BatchRepositoryInterface $batchRepository)
    {
       $this->eggSaleRepository = $eggSaleRepository;
       $this->birdSaleRepository = $birdSaleRepository;
       $this->batchRepository = $batchRepository;
    }


    public function create($data, $type)
    {
        if($type === 'egg')
        {
            DB::beginTransaction();
            try {
                // Process each item in the cart
                foreach ($data['items'] as $item) {
                    $eggs = Egg::select('id','date_collected','unit','total','is_sold','grade')
                        ->where('is_sold', false)
                        ->where('grade', $item['grade'])
                        ->selectRaw('(
                            CASE WHEN unit = "piece" THEN 1 
                            WHEN unit = "tray" THEN remaining
                            ELSE remaining END) as remaining')
                        ->orderBy('date_collected', 'ASC')
                        ->lockForUpdate()
                        ->get();

                    $sellQty = $item['unit'] == 'tray' ? $item['quantity'] * 30 : $item['quantity'];
                    $remaining = $sellQty;
                    $idsToMarkSold = [];
                    $partialRow = null;

                    foreach ($eggs as $egg) {
                        $pcs = match ($egg->unit) {
                            'piece' => 1,
                            'tray' => $egg->remaining,
                            default => $egg->remaining,
                        };

                        if ($remaining >= $pcs) {
                            $idsToMarkSold[] = $egg->id;
                            $remaining -= $pcs;
                        } else {
                            $remainingPcs = $pcs - $remaining;
                            $partialRow = [
                                'model' => $egg,
                                'remaining' => $remainingPcs
                            ];
                            break;
                        }
                    }

                    // Create egg sale record for this item
                    $saleData = array_merge($data, $item);
                    $this->eggSaleRepository->create($saleData);

                    // Mark eggs as sold
                    if (!empty($idsToMarkSold)) {
                        Egg::whereIn('id', $idsToMarkSold)
                            ->update(['is_sold' => 1]);
                    }

                    // Update partial row
                    if ($partialRow) {
                        $egg = $partialRow['model'];
                        $egg->remaining = $partialRow['remaining'];
                        $egg->save();
                    }
                }

                DB::commit();
                return true;
            } catch (\Throwable $e) {
                DB::rollBack();
                throw $e;
            }
        } else {
            $batch = $this->batchRepository->find($data['batch_id']);
            Log::info($batch);

            if( $data['count'] > $batch->current_quantity) {
                throw ValidationException::withMessages(['exceeds' => 'Culling exceeds live birds']);
            }

            $batch->current_quantity -= $data['count'];
            $batch->save();

            $bird = $this->birdSaleRepository->create($data);
            return $bird;
        }
    }

    public function summary(): array
    {
        $eggSales = (float) EggSale::query()->sum('total_amount');
        $birdSales = (float) BirdSale::query()->sum('total_amount');

        return [
            'egg_sales' =>$eggSales,
            'bird_sales' => $birdSales,
            'total_revenue' => $eggSales + $birdSales,
        ];
    }

    public function records(): array
    {
        $limit = (int) request('limit', 10);
        $search = request('search', '');

        $eggSales = EggSale::
            leftJoin('batches', 'egg_sales.batch_id', '=', 'batches.id')
            ->selectRaw('egg_sales.id as id')
            ->selectRaw('egg_sales.batch_id as batch_id')
            ->selectRaw('batches.batch_code as batch')
            ->selectRaw("'egg' as sale_type")
            ->selectRaw('egg_sales.sold_to as sold_to')
            ->selectRaw('egg_sales.sold_at as sold_at')
            ->selectRaw('egg_sales.quantity as quantity')
            ->selectRaw('egg_sales.unit as unit')
            ->selectRaw('egg_sales.grade as grade')
            ->selectRaw('egg_sales.price_per_unit as price')
            ->selectRaw('egg_sales.total_amount as total_amount')
            ->selectRaw('egg_sales.mode_of_payment as mode_of_payment')
            ->selectRaw('egg_sales.reference_no as reference_no')
            ->selectRaw('egg_sales.is_paid as is_paid')
            ->selectRaw('egg_sales.payment_status as payment_status')
            ->selectRaw('egg_sales.partial_amount as partial_amount')
            ->selectRaw('egg_sales.balance as balance')
            ->selectRaw('egg_sales.notes as notes')
            ->selectRaw('egg_sales.created_at as created_at')
            ->selectRaw('egg_sales.updated_at as updated_at');

        $birdSales = BirdSale::
            leftJoin('batches', 'bird_sales.batch_id', '=', 'batches.id')
            ->selectRaw('bird_sales.id as id')
            ->selectRaw('bird_sales.batch_id as batch_id')
            ->selectRaw('batches.batch_code as batch')
            ->selectRaw("'bird' as sale_type")
            ->selectRaw('bird_sales.sold_to as sold_to')
            ->selectRaw('bird_sales.sold_at as sold_at')
            ->selectRaw('bird_sales.count as quantity')
            ->selectRaw("'bird' as unit")
            ->selectRaw("'N/A' as grade")
            ->selectRaw('bird_sales.price_per_bird as price')
            ->selectRaw('bird_sales.total_amount as total_amount')
            ->selectRaw('bird_sales.mode_of_payment as mode_of_payment')
            ->selectRaw('bird_sales.reference_no as reference_no')
            ->selectRaw('bird_sales.is_paid as is_paid')
            ->selectRaw('bird_sales.payment_status as payment_status')
            ->selectRaw('bird_sales.partial_amount as partial_amount')
            ->selectRaw('bird_sales.balance as balance')
            ->selectRaw('bird_sales.notes as notes')
            ->selectRaw('bird_sales.created_at as created_at')
            ->selectRaw('bird_sales.updated_at as updated_at');

        $records = DB::query()
            ->fromSub($eggSales->unionAll($birdSales), 'sales')
            ->when($search, function ($query) use ($search) {
                $search = '%' . $search . '%';

                $query->where(function ($query) use ($search) {
                    $query->where('sale_type', 'like', $search)
                        ->orWhere('batch', 'like', $search)
                        ->orWhere('sold_to', 'like', $search)
                        ->orWhere('sold_at', 'like', $search)
                        ->orWhere('unit', 'like', $search)
                        ->orWhere('grade', 'like', $search)
                        ->orWhere('quantity', 'like', $search)
                        ->orWhere('price', 'like', $search)
                        ->orWhere('total_amount', 'like', $search)
                        ->orWhere('mode_of_payment', 'like', $search)
                        ->orWhere('reference_no', 'like', $search)
                        ->orWhere('payment_status', 'like', $search)
                        ->orWhere('partial_amount', 'like', $search)
                        ->orWhere('balance', 'like', $search)
                        ->orWhere('notes', 'like', $search);
                });
            })
            ->orderByDesc('sold_at')
            ->orderByDesc('created_at')
            ->paginate($limit);

        return [
            'data' => $records->items(),
            'pagination' => [
                'total' => $records->total(),
                'per_page' => $records->perPage(),
                'current_page' => $records->currentPage(),
                'last_page' => $records->lastPage(),
                'from' => $records->firstItem(),
                'to' => $records->lastItem(),
            ],
        ];
    }


    public function updateStatus($data, $id, $type)
    {
        if($type === 'egg') {
            $sale = EggSale::findOrFail($id);
        } else {
            $sale = BirdSale::findOrFail($id);
        }

        $sale->is_paid = $data['is_paid'];
        $sale->payment_status = $data['payment_status'];
        $sale->partial_amount = $data['partial_amount'] ?? null;
        $sale->balance = $data['balance'] ?? null;
        $sale->save();

        return $sale;
    }



}
