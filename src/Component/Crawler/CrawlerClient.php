<?php declare(strict_types=1);

namespace App\Component\Crawler;

use App\Component\Crawler\Bridge\HttpClientInterface;
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

        $xpath = new DOMXPath($dom);

        $domNodeList = $xpath->query($xpathExpression);

        if(!$domNodeList instanceof DOMNodeList || $domNodeList->length === 0) {
            throw new RuntimeException('Empty');
        }

        return $domNodeList;
    }


}
