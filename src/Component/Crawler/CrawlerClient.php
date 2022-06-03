<?php declare(strict_types=1);

namespace App\Component\Crawler;

use App\Component\Crawler\Bridge\HttpClientInterface;
use App\Component\Service\DomXpathService;
use DOMDocument;
use DOMNodeList;
use DOMXPath;
use RuntimeException;

final class CrawlerClient implements CrawlerClientInterface
{
    public function __construct(
        private HttpClientInterface $httpClient
    )
    {
    }

    public function get(string $url, string $xpathExpression): DOMNodeList
    {
        $html = $this->httpClient->getHtml($url);

        $dom = new DOMDocument();

        $dom->loadHTML($html);

        return DomXpathService::getNodeListByClass($dom, $xpathExpression);
    }


}
