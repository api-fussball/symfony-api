<?php declare(strict_types=1);

namespace App\Tests\Unit\Component\Crawler;

use App\Component\Crawler\Bridge\HttpClientInterface;
use App\Component\Crawler\CrawlerClient;
use App\Component\Service\Exception\XpathNotFound;
use PHPUnit\Framework\TestCase;

class CrawlerClientTest extends TestCase
{
    public function testExceptionWhenXPathNotFound()
    {
        $crowlerFaker = $this->createStub(HttpClientInterface::class);
        $crowlerFaker->method('getHtml')
            ->willReturn('<div><span>Unit-Test</span></div>');

        $this->expectException(XpathNotFound::class);
        $this->expectExceptionMessage('Not found');

        $crawlerClient = new CrawlerClient($crowlerFaker);
        $crawlerClient->get('test', 'foo');
    }
}
