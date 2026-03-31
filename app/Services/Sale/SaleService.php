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
            $eggs = Egg::select('id','date_collected','unit','total','is_sold','grade')
            ->where('is_sold', false)
            ->where('grade', $data['grade'])
           ->selectRaw('(
            CASE WHEN unit = "piece" THEN 1 
            WHEN unit = "tray" THEN remaining
            ELSE remaining END) as remaining')
            ->orderBy('date_collected', 'ASC')
             ->lockForUpdate()
            ->get();

            $totalAvailable = Egg::where('is_sold', false)
            ->where('grade', $data['grade'])
            ->selectRaw('SUM(
                CASE 
                    WHEN unit = "piece" THEN 1 
                    WHEN unit = "tray" THEN remaining
                    ELSE remaining 
                END
            ) as total_pcs')
            ->value('total_pcs');

            $sellQty = $data['unit'] == 'tray' ? $data['quantity'] * 30 : $data['quantity'];

             if ($totalAvailable < $sellQty) {
                    throw ValidationException::withMessages(['out_of_stock' => 'Grade/Size selected out of stock!']);
            }

             $remaining = $sellQty;
             $idsToMarkSold = [];
             $partialRow = null;

             foreach ($eggs as $egg){

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
            // DB Create and Update

             $this->eggSaleRepository->create($data);

            if (!empty($idsToMarkSold)) {
                    Egg::whereIn('id', $idsToMarkSold)
                        ->update(['is_sold' => 1]);
            }

            if ($partialRow) {

                $egg = $partialRow['model'];
                $remaining = $partialRow['remaining'];

                $egg->remaining = $remaining;
                $egg->save(); 
            }

            return $eggs;
        }else{
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
            ->selectRaw('egg_sales.price_per_unit as price')
            ->selectRaw('egg_sales.total_amount as total_amount')
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
            ->selectRaw('bird_sales.price_per_bird as price')
            ->selectRaw('bird_sales.total_amount as total_amount')
            ->selectRaw('bird_sales.notes as notes')
            ->selectRaw('bird_sales.created_at as created_at')
            ->selectRaw('bird_sales.updated_at as updated_at');

        $records = DB::query()
            ->fromSub($eggSales->unionAll($birdSales), 'sales')
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



}
