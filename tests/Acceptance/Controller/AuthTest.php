<?php declare(strict_types=1);

namespace App\Tests\Acceptance\Controller;

use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AuthTest extends WebTestCase
{
    private KernelBrowser $client;
    private ObjectManager $entityManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();

        $container = self::getContainer();
        $this->entityManager = $container->get('doctrine')->getManager();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $connection = $this->entityManager->getConnection();
        $connection->executeQuery('TRUNCATE user');

        $connection->close();
    }

    public function testAuthRegister(): void
    {
        $header = [
            'CONTENT_TYPE' => 'application/json',
        ];

        $content = '{"email":"ninja@sec//ret.com"}';

        $this->client->request('POST', '/auth/register', [], [], $header, $content);

        self::assertResponseStatusCodeSame(200);

        $response = $this->client->getResponse();

        self::assertTrue($response->headers->contains('Content-Type', 'application/json'));

        $responseRequest = json_decode($response->getContent(), true);

        self::assertSame(
            'Please copy the token. After leaving the page, copying again is not possible.',
            $responseRequest['message']
        );
        self::assertSame('ninja@secret.com', $responseRequest['data']['email']);
        self::assertSame(64, strlen($responseRequest['data']['token']));
        self::assertTrue($responseRequest['success']);
    }

    public function testAuthRegisterWithIncorrectEmail(): void
    {
        $header = [
            'CONTENT_TYPE' => 'application/json',
        ];

        $content = '{"email":"ninja@secret"}';

        $this->client->request('POST', '/auth/register', [], [], $header, $content);

        self::assertResponseStatusCodeSame(400);

        $response = $this->client->getResponse();

        self::assertTrue($response->headers->contains('Content-Type', 'application/json'));

        $responseRequest = json_decode($response->getContent(), true);

        self::assertSame('Email "ninja@secret" is not valid!', $responseRequest['message']);
        self::assertEmpty($responseRequest['data']);
        self::assertFalse($responseRequest['success']);
    }

    public function testAuthRegisterWithoutEmail(): void
    {
        $header = [
            'CONTENT_TYPE' => 'application/json',
        ];

        $content = '{"name":"john"}';

        $this->client->request('POST', '/auth/register', [], [], $header, $content);

        self::assertResponseStatusCodeSame(400);

        $response = $this->client->getResponse();

        self::assertTrue($response->headers->contains('Content-Type', 'application/json'));

        $responseRequest = json_decode($response->getContent(), true);

        self::assertSame('Error! Field email not found', $responseRequest['message']);
        self::assertEmpty($responseRequest['data']);
        self::assertFalse($responseRequest['success']);
    }

}
