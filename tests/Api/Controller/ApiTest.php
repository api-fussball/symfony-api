<?php declare(strict_types=1);

namespace App\Tests\Api\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApiTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();
    }

    public function testClubInfo()
    {
        $this->client->request('GET', '/api/club/00ES8GN91400002IVV0AG08LVUPGND5I');

        self::assertResponseStatusCodeSame(200);

        $response = $this->client->getResponse();

        self::assertTrue($response->headers->contains('Content-Type', 'application/json'));

        $responseRequest = json_decode($response->getContent(), true);

        self::assertArrayHasKey('data', $responseRequest);
        self::assertSame('Herren - Fühlingen I', $responseRequest['data'][0]['name']);
    }

}
