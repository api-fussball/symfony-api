<?php declare(strict_types=1);

namespace App\Component\User\Model;

use App\Component\Dto\UserTransfer;

interface TokenInterface
{
    public function save(string $userMail): UserTransfer;
}
