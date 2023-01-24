<?php declare(strict_types=1);

namespace App\Tests\Integration\Component\User;

use App\Component\Dto\UserTransfer;
use App\Component\User\UserFacade;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserFacadeTest extends KernelTestCase
{
    private UserFacade $userFacade;
    private ObjectManager $entityManager;

    protected function setUp(): void
    {
        parent::setUp();

        $container = self::getContainer();

        $this->entityManager = $container->get('doctrine')->getManager();
        $this->userFacade = $container->get(UserFacade::class);
    }


    protected function tearDown(): void
    {
        parent::tearDown();

        $connection = $this->entityManager->getConnection();
        $connection->executeQuery('TRUNCATE user');

        $connection->close();
    }

    public function testInsertUser()
    {
        $email = 'mega@unit.te';

        $userTransfer = $this->userFacade->save($email);

        self::assertNotEmpty($userTransfer->token);
        self::assertSame(64, strlen($userTransfer->token));

        self::assertSame($email, $userTransfer->email);
        self::assertGreaterThan(0, $userTransfer->id);
    }

    public function testChaneTokenWhenUserExistInDb()
    {
        $email = 'new_not_set@unit.te';
        $this->userFacade->save($email);

        $email = 'mega@unit.te';

        $userTransfer = $this->userFacade->save($email);

        self::assertSame(2, $userTransfer->id);
        $token = $userTransfer->token;
        $userTransfer = $this->userFacade->save($email);
        self::assertNotEmpty($userTransfer->token);
        self::assertSame(64, strlen($userTransfer->token));

        self::assertNotSame($token, $userTransfer->token);
    }

    public function testIfIssetToken()
    {
        $email = 'mega@unit.te';

        $token = $this->userFacade->save($email)->token;

       self::assertTrue($this->userFacade->issetToken($token));
    }

    public function testIssetTokenWhenIsIncorrectToken()
    {
        $email = 'mega@unit.te';
        $this->userFacade->save($email);

        self::assertFalse($this->userFacade->issetToken(bin2hex(random_bytes(64))));
    }
}
