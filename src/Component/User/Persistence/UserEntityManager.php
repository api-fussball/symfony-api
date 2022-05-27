<?php declare(strict_types=1);

namespace App\Component\User\Persistence;

use App\Component\Dto\UserTransfer;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;

final class UserEntityManager implements UserEntityManagerInterface
{
    public function __construct(
        private readonly ManagerRegistry $doctrine
    )
    {
    }

    public function saveUser(UserTransfer $userTransfer): UserTransfer
    {
        $userEntity = $this->doctrine->getRepository(User::class)
            ->findOneBy(['email' => $userTransfer->email]);

        if(!$userEntity instanceof User) {
            $userEntity = new User();
            $userEntity->email = $userTransfer->email;
        }

        $userEntity->token = $userTransfer->token;

        $this->flush($userEntity);

        $userTransfer->id = $userEntity->id;

        return $userTransfer;
    }

    private function flush(User $userEntity): void
    {
        $manager = $this->doctrine->getManager();
        $manager->persist($userEntity);

        $manager->flush();
    }
}
