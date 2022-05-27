<?php declare(strict_types=1);

namespace App\Component\User\Persistence;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;

final class UserRepository implements UserRepositoryInterface
{
    public function __construct(
        private readonly ManagerRegistry $doctrine
    )
    {
    }

    public function issetUserByToken(string $token): bool
    {
        $user = $this->doctrine->getRepository(User::class)
            ->findOneBy(['token' => $token]);

        return $user instanceof User;
    }

}
