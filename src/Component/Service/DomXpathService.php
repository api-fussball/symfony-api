<?php declare(strict_types=1);

namespace App\Component\Service;

use App\Component\Service\Exception\XpathNotFound;

final class DomXpathService
{
    public static function getNodeListByClass(\DOMDocument $dom, string $xPath): \DOMNodeList
    {
        $xpath = new \DOMXPath($dom);

        /** @var \DOMNodeList $domNodeList */
        $domNodeList = $xpath->query($xPath);

        if ($domNodeList->length === 0) {
            throw new XpathNotFound();
        }

        return $domNodeList;
    }
}
