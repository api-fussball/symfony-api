<?php declare(strict_types=1);

namespace App\Component\Crawler\Bridge;

use RuntimeException;
use Symfony\Contracts\HttpClient\HttpClientInterface as SymfonyHttpClientInterface;

final class HttpClient implements HttpClientInterface
{
    public function __construct(private SymfonyHttpClientInterface $client)
    {
    }

    public function getHtml(string $url): string
    {
        $response = $this->client->request(
            'GET',
            'https://fussball.de' . $url
        );

        $content = $response->getContent();

        if(empty($content)) {
            throw new RuntimeException('Empty Content');
        }

        return $content;
    }
}
