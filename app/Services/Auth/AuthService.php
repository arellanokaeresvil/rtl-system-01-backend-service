<?php

namespace App\Services\Auth;

use App\Http\Resources\User\UserResource;
use Illuminate\Support\Facades\Auth;
use App\Repository\User\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService implements AuthServiceInterface
{
  private $userRepository;

  public function __construct(UserRepositoryInterface $userRepository)
  {
      $this->userRepository = $userRepository;
  }

    public function register(array $data)
    {
        // Implement registration logic
    }

    public function login(array $data)
    {
        $user = $this->userRepository->findByEmail($data['email']);

        if($user) {
            if(Hash::check($data['password'], $user->password)) {

                if($user->tokens){
                    foreach($user->tokens as $token){
                        $token->delete();
                    }
                }
                return [
                    'token' => $user->createToken('auth_token')->plainTextToken,
                    'user' =>  new UserResource($user)
                ];
            }
        }

       throw ValidationException::withMessages([
            'credentials' => __('auth.failed')
       ]);
    }


}
