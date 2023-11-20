<?php declare(strict_types=1);

namespace App\Tests\Unit\Component\Crawler\Bridge;

use App\Component\Crawler\Bridge\HttpClient;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;

class HttpClientTest extends TestCase
{
    public function testExceptionWhenContentNotFound()
    {
        $httpClientMock = new MockHttpClient(
            new MockResponse('')
        );

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Empty Content for url: https://www.fussball.de/test');

        $crawlerClient = new HttpClient($httpClientMock);
        $crawlerClient->getHtml('/test');
    }

    public function testGetHtml()
    {
        $httpClientMock = new MockHttpClient(
            new MockResponse('unit foo hoho')
        );

        $crawlerClient = new HttpClient($httpClientMock);

        self::assertSame('unit foo hoho', $crawlerClient->getHtml('/test'));;
    }
}
