<?php declare(strict_types=1);

namespace App\Component\User;

use App\Component\Dto\UserTransfer;
use App\Component\User\Model\TokenInterface;
use App\Component\User\Persistence\UserRepositoryInterface;

final class UserFacade implements UserFacadeInterface
{
    public function __construct(
        private readonly TokenInterface          $token,
        private readonly UserRepositoryInterface $userRepository,
    )
    {
    }

    public function save(string $email): UserTransfer
    {
        return $this->token->save($email);
    }

    public function issetToken(string $token): bool
    {
        return $this->userRepository->issetUserByToken($token);
    }
}
