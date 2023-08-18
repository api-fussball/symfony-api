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
        $url = 'https://www.fussball.de' . $url;
        $response = $this->client->request(
            'GET',
            $url
        );

        $content = $response->getContent();

        if(empty($content)) {
            throw new RuntimeException('Empty Content for url: ' . $url);
        }

        return $content;
    }
}
