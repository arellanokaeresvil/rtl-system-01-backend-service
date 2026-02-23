<?php

namespace App\Http\Controllers\Sales;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Egg\EggCollection;
use App\Http\Requests\Sale\BirdSaleRequest;
use App\Http\Resources\Sale\BirdSaleResource;
use App\Http\Resources\Sale\EggSaleCollection;
use App\Http\Resources\Sale\BirdSaleCollection;
use App\Services\Utils\ResponseServiceInterface;
use App\Repository\Sale\BirdSaleRepositoryInterface;

class BirdSaleController extends Controller
{
    private $responseService;
    private $birdSaleRepository;
    private $name = 'Bird Sale';
    

    public function __construct(ResponseServiceInterface $responseService, BirdSaleRepositoryInterface $birdSaleRepository)
    {
        $this->responseService = $responseService;
        $this->birdSaleRepository = $birdSaleRepository;
    }

        public function index()
    {
        $search = request('search', null); 
        $sales = $this->birdSaleRepository->list(['search' => $search]);
        return $this->responseService->successResponse($this->name, new BirdSaleCollection($sales));
    }

    public function store(BirdSaleRequest $request)
    {
        $egg_sale = $this->birdSaleRepository->create($request->validated());
        return $this->responseService->storeResponse($this->name, new BirdSaleResource($egg_sale));
    }
}
