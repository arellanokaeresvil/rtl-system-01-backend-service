<?php

namespace App\Repository\User;

use App\Models\User;
use App\Repository\Base\BaseRepository;
use Illuminate\Validation\ValidationException;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    public function findByEmail(string $email): ?User
    {
        $user = User::where('email', $email)->first();
        if ($user) {
            return $user;
        } else {
            return null;
        }
    }

}
