<?php declare(strict_types=1);

namespace App\Component\User\Persistence;

interface UserRepositoryInterface
{
    public function issetUserByToken(string $token): bool;
}
