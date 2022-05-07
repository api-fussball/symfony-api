<?php declare(strict_types=1);

namespace App\Component\Crawler;

use DOMNodeList;

interface CrawlerClientInterface
{
    public function get(string $url, string $xpathExpression): DOMNodeList;
}
