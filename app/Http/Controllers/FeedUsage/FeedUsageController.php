<?php

namespace App\Http\Controllers\FeedUsage;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Utils\ResponseService;
use App\Services\Feed\FeedServiceInterface;
use App\Http\Requests\Feed\FeedUsageRequest;
use App\Http\Resources\Feed\FeedUsageResource;
use App\Http\Resources\Feed\FeedUsageCollection;
use App\Repository\Feed\FeedUsageRepositoryInterface;

class FeedUsageController extends Controller
{

    private $responseService;
    private $feedSevice;
    private $feedUsageRepository;
    private $name = 'Feed Usage';
    public function __construct(ResponseService $responseService, FeedServiceInterface $feedSevice, FeedUsageRepositoryInterface $feedUsageRepository)
    {
        $this->responseService = $responseService;
        $this->feedSevice = $feedSevice;
        $this->feedUsageRepository = $feedUsageRepository;
    }

    public function index()
    {
        $search = request('search', null); 
        $feedUsages = $this->feedUsageRepository->list(['search' => $search]);
        return $this->responseService->successResponse($this->name, new FeedUsageCollection($feedUsages));
    }

    public function store(FeedUsageRequest  $request)
    {
        $validatedData = $request->validated();
        $feedUsage = $this->feedSevice->deduct($validatedData);
        return $this->responseService->storeResponse($this->name, new FeedUsageResource($feedUsage));
    }
}
