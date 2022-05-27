<?php declare(strict_types=1);

namespace App\Component\Dto;

final class UserTransfer
{
    public int $id = 0;
    public string $email = '';
    public string $token = '';
}
