<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Http\Requests\Sale\BirdSaleRequest;
use App\Http\Requests\Sale\EggSaleRequest;
use App\Http\Resources\Sale\BirdSaleResource;
use App\Http\Resources\Sale\EggSaleResource;
use App\Services\Sale\SaleServiceInterface;
use App\Services\Utils\ResponseServiceInterface;
use Illuminate\Http\Request;

class SalesController extends Controller
{
   private $responseService;
    private $saleService;
   private $name = 'Sale';

   public function __construct(ResponseServiceInterface $responseService, SaleServiceInterface $saleService)
   {
        $this->responseService = $responseService;
        $this->saleService = $saleService;
   }

    public function store_bird_sale(BirdSaleRequest $request)
    {
         $bird_sale = $this->saleService->create($request->validated(), 'bird');
        return $this->responseService->storeResponse($this->name, new BirdSaleResource($bird_sale));
    }
    public function store_egg_sale(EggSaleRequest $request)
    {
         $egg_sale = $this->saleService->create($request->validated(), 'egg');
        return $this->responseService->storeResponse($this->name, new $egg_sale);
    }

   public function summary(Request $request){
    $data = $this->saleService->summary();
    return $this->responseService->successResponse($this->name, $data);
   }

   public function records(Request $request){
    $data = $this->saleService->records();
    return $this->responseService->successResponse($this->name, $data);
   }
  
}
