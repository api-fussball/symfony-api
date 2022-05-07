<?php declare(strict_types=1);

namespace App\Component\Crawler;

use App\Component\Crawler\Bridge\HttpClientInterface;

final class CrawlerClient implements CrawlerClientInterface
{
    public function __construct(
        private HttpClientInterface $httpClient
    )
    {
    }

    public function get(string $url, string $xpathExpression): \DOMNodeList
    {
        $html = $this->httpClient->getHtml($url);

        $dom = new \DOMDocument();

        /**
         * $html is no empty, i check this in getHtml Method
         * @psalm-suppress ArgumentTypeCoercion
         */
        $dom->loadHTML($html);

        $xpath = new \DOMXPath($dom);

        $domNodeList = $xpath->query($xpathExpression);

        if(!$domNodeList instanceof \DOMNodeList || $domNodeList->length === 0) {
            throw new \RuntimeException('Empty');
        }

        return $domNodeList;
    }


}
