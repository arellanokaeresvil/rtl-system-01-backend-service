<?php

namespace App\Http\Controllers\Mortality;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Bird\BirdServiceInterface;
use App\Services\Utils\ResponseServiceInterface;
use App\Http\Requests\Mortality\MortalityRequest;
use App\Http\Resources\Mortality\MortalityResource;
use App\Http\Resources\Mortality\MortalityCollection;
use App\Repository\Mortality\MortalityRepositoryInterface;

class MortalityController extends Controller
{
    private $responseService;
    private $mortalityRepository;
    private $birdService;
    private $name = 'Mortality';

    public function __construct( ResponseServiceInterface $responseService, MortalityRepositoryInterface $mortalityRepository, BirdServiceInterface $birdService)
    {
        $this->responseService = $responseService;
        $this->mortalityRepository = $mortalityRepository;
        $this->birdService = $birdService;
    }

       public function index()
    {
        $search = request('search', null); 
        $mortalities = $this->mortalityRepository->list(['search' => $search]);
        return $this->responseService->successResponse($this->name, new MortalityCollection($mortalities));
    }

    public function store(MortalityRequest $request)
    {
        $data = $this->birdService->applyMortality($request->validated());
        return $this->responseService->storeResponse($this->name, new MortalityResource($data));
    }

}
