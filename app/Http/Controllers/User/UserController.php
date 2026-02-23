<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\UserRequest;
use App\Http\Resources\User\UserCollection;
use App\Http\Resources\User\UserOptionResource;
use App\Http\Resources\User\UserResource;
use App\Repository\User\UserRepositoryInterface;
use App\Services\Utils\ResponseServiceInterface;


class UserController extends Controller
{
    private $responseService;
    private $userRepository;
    private $name = 'User';

    public function __construct(ResponseServiceInterface $responseService, UserRepositoryInterface $userRepository){
        $this->responseService = $responseService;
        $this->userRepository = $userRepository;
    }

    public function index()
    {
        $search = request('search', null); 
        $users = $this->userRepository->list(['search' => $search]);
        return $this->responseService->successResponse($this->name,new UserCollection($users));
    }

    public function store(UserRequest $request)
    {
        $user = $this->userRepository->create($request->validated());
        return $this->responseService->storeResponse($this->name, new UserResource($user));
    }

    public function show($id)
    {
        $user = $this->userRepository->find($id);
        return $this->responseService->showResponse($this->name, new UserResource($user));
    }

    public function update(UserRequest $request, $id)
    {
        $user = $this->userRepository->update($id, $request->validated());
        return $this->responseService->updateResponse($this->name, new UserResource($user));
    }

    public function destroy($id)
    {
        $this->userRepository->delete($id);
        return $this->responseService->deleteResponse($this->name);
    }

    public function restore($id)
    {
        $user = $this->userRepository->restore($id);
        return $this->responseService->restoreResponse($this->name, new UserResource($user));
    }

    public function getOptions()
    {
        $users = $this->userRepository->all();
        return $this->responseService->successResponse($this->name, new UserOptionResource($users));
    }
}
