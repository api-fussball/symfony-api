<?php declare(strict_types=1);

namespace App\Component\User\Persistence;

use App\Component\Dto\UserTransfer;

interface UserEntityManagerInterface
{
    public function saveUser(UserTransfer $userTransfer): UserTransfer;
}
