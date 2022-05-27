<?php declare(strict_types=1);

namespace App\Tests\Unit\Component\User\Model;

use App\Component\Dto\UserTransfer;
use App\Component\User\Model\Token;
use App\Component\User\Persistence\UserEntityManagerInterface;
use PHPUnit\Framework\TestCase;

class TokenTest extends TestCase
{
    public function testSave()
    {
        $userEntityManagerStub = new class implements UserEntityManagerInterface {

            public function saveUser(UserTransfer $userTransfer): UserTransfer
            {
                $userTransfer->id = 1;

                return $userTransfer;
            }

        };

        $token = new Token($userEntityManagerStub);

        $email = 'mega@unit.te';

        $userTransfer = $token->save($email);

        self::assertNotEmpty($userTransfer->token);
        self::assertSame(64, strlen($userTransfer->token));

        self::assertSame($email, $userTransfer->email);
        self::assertSame(1, $userTransfer->id);
    }

    public function testFailSave()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Error on save user');

        $userEntityManagerStub = $this->createStub(UserEntityManagerInterface::class);

        $userEntityManagerStub->method('saveUser')
            ->willReturn(new UserTransfer());

        $token = new Token($userEntityManagerStub);
        $token->save('mega@unit.te');
    }

    public function testFailSaveWhenEmailIsInccorect()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Email "no_email@he" is not valid!');

        $userEntityManagerStub = $this->createStub(UserEntityManagerInterface::class);

        $token = new Token($userEntityManagerStub);
        $token->save('no_email@he');
    }
}
