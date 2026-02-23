<?php

namespace App\Http\Controllers\Batch;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Batch\BatchRequest;
use App\Http\Resources\Batch\BatchResource;
use App\Http\Resources\Batch\BatchCollection;
use App\Services\Batch\BatchServiceInterface;
use App\Services\Utils\ResponseServiceInterface;
use App\Http\Resources\Batch\BatchOptionResource;
use App\Repository\Batch\BatchRepositoryInterface;

class BatchController extends Controller
{
    private $responseService;
    private $batchRepository;
    private $batchService;
    private $name = 'Batch';

    public function __construct(ResponseServiceInterface $responseService, BatchRepositoryInterface $batchRepository, BatchServiceInterface $batchService)
    {
        $this->responseService = $responseService;
        $this->batchRepository = $batchRepository;
        $this->batchService = $batchService;
    }

    public function index()
    {
        $search = request('search', null); 
        $batches = $this->batchRepository->list(['search' => $search]);
        return $this->responseService->successResponse($this->name, new BatchCollection($batches));
    }

    public function show($id)
    {
        $batch = $this->batchRepository->find($id);
        return $this->responseService->showResponse($this->name, new BatchResource($batch));
    }

    public function store(BatchRequest $request)
    {
        $batch = $this->batchService->create($request->validated());
        return $this->responseService->storeResponse($this->name, new BatchResource($batch));
    }

    public function update(BatchRequest $request, $id)
    {
        $batch = $this->batchRepository->update($id, $request->validated());
        return $this->responseService->updateResponse($this->name, new BatchResource($batch));
    }

    public function destroy($id)
    {
        $this->batchRepository->delete($id);
        return $this->responseService->deleteResponse($this->name);
    }

    public function restore($id)
    {
        $batch = $this->batchRepository->restore($id);
        return $this->responseService->restoreResponse($this->name, new BatchResource($batch));
    }

   public function getOptions()
    {
        $types = $this->batchRepository->Options();
        return $this->responseService->successResponse($this->name, BatchOptionResource::collection($types));
    }

}
