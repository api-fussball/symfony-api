<?php declare(strict_types=1);

namespace App\Tests\Acceptance\Controller;

use App\Component\User\UserFacadeInterface;
use App\Controller\Api;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApiTest extends WebTestCase
{
    private KernelBrowser $client;
    private ObjectManager $entityManager;
    private string $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();

        $container = self::getContainer();
        $this->entityManager = $container->get('doctrine')->getManager();

        $this->token = $container->get(UserFacadeInterface::class)
            ->save('ninja@test-un.it')
            ->token;
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $connection = $this->entityManager->getConnection();
        $connection->executeQuery('TRUNCATE user');

        $connection->close();
    }

    public function testApiWithoutAuthHeader(): void
    {
        $this->client->request('GET', '/api/club/not_found');

        self::assertResponseStatusCodeSame(403);

        $response = $this->client->getResponse();

        self::assertTrue($response->headers->contains('Content-Type', 'application/json'));

        $responseRequest = json_decode($response->getContent(), true);

        self::assertArrayHasKey('data', $responseRequest);
        self::assertEmpty($responseRequest['data']);

        self::assertArrayHasKey('traces', $responseRequest);
        self::assertGreaterThan(3, $responseRequest['traces']);

        self::assertArrayHasKey('success', $responseRequest);
        self::assertFalse($responseRequest['success']);

        self::assertArrayHasKey('message', $responseRequest);
        self::assertSame(
            'Token in header: "x-auth-token" not found',
            $responseRequest['message']
        );
    }

    public function testApiWithInncorectAuthHeader(): void
    {
        $token = substr($this->token, 1, -1);
        $this->client->request(
            method: 'GET',
            uri: '/api/club/not_found',
            server: [
                'HTTP_' . Api::HEADER_AUTH_NAME => $token,
            ]
        );

        self::assertResponseStatusCodeSame(401);

        $response = $this->client->getResponse();

        self::assertTrue($response->headers->contains('Content-Type', 'application/json'));

        $responseRequest = json_decode($response->getContent(), true);

        self::assertArrayHasKey('data', $responseRequest);
        self::assertEmpty($responseRequest['data']);

        self::assertArrayHasKey('traces', $responseRequest);
        self::assertGreaterThan(3, $responseRequest['traces']);

        self::assertArrayHasKey('success', $responseRequest);
        self::assertFalse($responseRequest['success']);

        self::assertArrayHasKey('message', $responseRequest);
        self::assertSame(
            sprintf('Token "%s" not found', $token),
            $responseRequest['message']
        );
    }

}
