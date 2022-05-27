<?php declare(strict_types=1);

namespace App\Component\User;

use App\Component\Dto\UserTransfer;

interface UserFacadeInterface
{
    public function save(string $email): UserTransfer;

    public function issetToken(string $token): bool;
}
