<?php

namespace App\Http\Controllers\Sales;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Egg\EggCollection;
use App\Http\Requests\Sale\EggSaleRequest;
use App\Http\Resources\Sale\EggSaleResource;
use App\Http\Resources\Sale\EggSaleCollection;
use App\Services\Utils\ResponseServiceInterface;
use App\Repository\Sale\EggSaleRepositoryInterface;

class EggSaleController extends Controller
{
    private $responseService;
    private $eggSaleRepository;
    private $name = 'Egg Sale';
    

    public function __construct(ResponseServiceInterface $responseService, EggSaleRepositoryInterface $eggSaleRepository)
    {
        $this->responseService = $responseService;
        $this->eggSaleRepository = $eggSaleRepository;
    }

        public function index()
    {
        $search = request('search', null); 
        $sales = $this->eggSaleRepository->list(['search' => $search]);
        return $this->responseService->successResponse($this->name, new EggSaleCollection($sales));
    }

      public function store(EggSaleRequest $request)
    {
        $egg_sale = $this->eggSaleRepository->create($request->validated());
        return $this->responseService->storeResponse($this->name, new EggSaleResource($egg_sale));
    }
}
