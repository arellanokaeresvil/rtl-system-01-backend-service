<?php

namespace App\Http\Controllers\Egg;

use App\Http\Controllers\Controller;
use App\Http\Requests\Egg\CustomizeRequest;
use App\Http\Requests\Egg\EggRequest;
use App\Http\Resources\Egg\EggResource;
use App\Http\Resources\Egg\EggCollection;
use App\Services\Egg\EggServiceInterface;
use App\Http\Resources\Egg\EggBatchCollection;
use App\Repository\Egg\EggRepositoryInterface;
use App\Services\Utils\ResponseServiceInterface;

class EggController extends Controller
{
    private $responseService;
    private $eggRepository;
    private $eggService;
    private $name = 'Egg';

    public function __construct(ResponseServiceInterface $responseService, EggRepositoryInterface $eggRepository, EggServiceInterface $eggService)
    {
        $this->responseService = $responseService;
        $this->eggRepository = $eggRepository;
        $this->eggService = $eggService;
    }

    public function index()
    {
        if(request()->has('getByGrade')) {
            return $this->getByGrade();
        }
        if(request()->has('getByBatch')) {
                return $this->getByBatch();
        }
        if(request()->has('getAvailableEgg')) {
                return $this->getAvailableEgg();
        }
        $search = request('search', null); 
        $eggs = $this->eggRepository->list(['search' => $search]);
        return $this->responseService->successResponse($this->name, new EggCollection($eggs));
    }

    private function getAvailableEgg()
    {
        $eggs = $this->eggService->getAvailableEgg();
        return $this->responseService->successResponse($this->name, $eggs);
    }
    private function getByGrade()
    {
        $eggs = $this->eggService->getByGrade();
        return $this->responseService->successResponse($this->name, $eggs);
    }
    private function getByBatch()
    {
        $eggs = $this->eggService->getByBatch();
        return $this->responseService->successResponse($this->name, new EggBatchCollection($eggs));
    }

    public function show($id)
    {
        $egg = $this->eggRepository->find($id);
        return $this->responseService->showResponse($this->name, new EggResource($egg));
    }

    public function store(CustomizeRequest $request)
    {
        $egg = $this->eggService->storePerPiece($request->validated());
        return $this->responseService->storeResponse($this->name,$egg);
    }

    public function storePerTray(CustomizeRequest $request)
    {
        $egg = $this->eggService->storePerTray($request->validated());
        return $this->responseService->storeResponse($this->name, $egg);
    }

    public function storeCustomize(CustomizeRequest $request)
    {
        $egg = $this->eggService->storeCustomize($request->validated());
        return $this->responseService->storeResponse($this->name, $egg);
    }

    public function update(EggRequest $request, $id)
    {
        $egg = $this->eggRepository->update($id, $request->validated());
        return $this->responseService->updateResponse($this->name, new EggResource($egg));
    }

}
