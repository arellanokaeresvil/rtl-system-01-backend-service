<?php

namespace App\Http\Controllers\Culling;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Utils\ResponseService;
use App\Services\Bird\BirdServiceInterface;
use App\Http\Requests\Culling\CullingRequest;
use App\Http\Resources\Culling\CullingResource;
use App\Http\Resources\Culling\CullingCollection;
use App\Repository\Culling\CullingRepositoryInterface;

class CullingController extends Controller
{
    private $responseService;
    private $cullingRepository;
    private $birdService;
    private $name = 'Culling';

    public function __construct(ResponseService $responseService, BirdServiceInterface $birdService, CullingRepositoryInterface $cullingRepository)
    {
        $this->responseService = $responseService;
        $this->birdService = $birdService;
        $this->cullingRepository = $cullingRepository;
    }

       public function index()
    {
        $search = request('search', null);
        $cullings = $this->cullingRepository->list(['search' => $search]);
        return $this->responseService->successResponse($this->name, new CullingCollection($cullings));
    }

    public function store(CullingRequest $request)
    {
        $culling = $this->birdService->applyCulling($request->validated());
        return $this->responseService->successResponse($this->name, new CullingResource($culling), 201);
    }

}
