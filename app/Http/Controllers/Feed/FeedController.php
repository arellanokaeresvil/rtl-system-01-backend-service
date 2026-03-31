<?php

namespace App\Http\Controllers\Feed;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Feed\FeedRequest;
use App\Http\Resources\Feed\FeedResource;
use App\Http\Resources\Feed\FeedCollection;
use App\Http\Resources\Feed\FeedOptionResource;
use App\Services\Feed\FeedServiceInterface;
use App\Repository\Feed\FeedRepositoryInterface;
use App\Services\Utils\ResponseServiceInterface;

class FeedController extends Controller
{
    private $responseService;
    private $feedRepository;
    private $feedService;
    private $name = 'Feeds';

    public function __construct(ResponseServiceInterface $responseService, FeedRepositoryInterface $feedRepository, FeedServiceInterface $feedService)
    {
        $this->responseService = $responseService;
        $this->feedRepository = $feedRepository;
        $this->feedService = $feedService;
    }

    public function index()
    {
        if(request()->has('getByType')) {
            return $this->getByType();
        }
        $search = request('search', null);
        $feeds = $this->feedRepository->list(['search' => $search]);
        return $this->responseService->successResponse($this->name, new FeedCollection($feeds));
    }

    public function getByType()
    {
        $feeds = $this->feedService->getByType();
        return $this->responseService->successResponse($this->name, $feeds);
    }

    public function show($id)
    {
        $feed = $this->feedRepository->find($id);
        return $this->responseService->showResponse($this->name, new FeedResource($feed));
    }

    public function store(FeedRequest $request)
    {
        $feed = $this->feedService->create($request->validated());
        return $this->responseService->storeResponse($this->name, new FeedResource($feed));
    }

    public function update(FeedRequest $request, $id)
    {
        $feed = $this->feedRepository->update($id, $request->validated());
        return $this->responseService->updateResponse($this->name, new FeedResource($feed));
    }

    public function getOptions(Request $request)
    {
        $types = $this->feedRepository->Options($request);
        return $this->responseService->successResponse($this->name, FeedOptionResource::collection($types));
    }

}
