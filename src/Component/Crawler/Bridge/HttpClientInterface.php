<?php declare(strict_types=1);

namespace App\Component\Crawler\Bridge;

interface HttpClientInterface
{
    public function getHtml(string $url): string;
}
