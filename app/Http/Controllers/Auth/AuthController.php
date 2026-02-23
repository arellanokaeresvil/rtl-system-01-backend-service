<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\User\UserResource;
use App\Services\Auth\AuthServiceInterface;
use App\Services\Utils\ResponseServiceInterface;

class AuthController extends Controller
{
    private $authService;
    private $responseService;

    public function __construct(AuthServiceInterface $authService, ResponseServiceInterface $responseService )
    {
        $this->authService = $authService;
        $this->responseService = $responseService;
    }

    public function login(LoginRequest $request)
    {
        $token = $this->authService->login($request->validated());
        return $this->responseService->resolveResponse("Login successful", $token);
    }
    
        public function authUser()
    {
        $user = new UserResource(Auth::user());
        return $this->responseService->resolveResponse('Auth User', $user);
    }

     public function logout()
    {
        $user = Auth::user();

        $user->currentAccessToken()->delete(); // current token
        // $user->tokens()->delete(); // all tokens

        return $this->responseService->resolveResponse("Logout Successful", null);
    }
}
