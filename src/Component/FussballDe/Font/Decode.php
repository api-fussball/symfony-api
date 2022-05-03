<?php declare(strict_types=1);

namespace App\Component\FussballDe\Font;

use Symfony\Contracts\HttpClient\HttpClientInterface;

final class Decode implements DecodeInterface
{
    private const MAP = [
        'hyphen' => '-',
        'zero' => '0',
        'one' => '1',
        'two' => '2',
        'three' => '3',
        'four' => '4',
        'five' => '5',
        'six' => '6',
        'seven' => '7',
        'eight' => '8',
        'nine' => '9',
    ];

    private const URL = 'https://www.fussball.de/export.fontface/-/format/woff/id/%s/type/font';
    private const SHELL_COMMAND = 'cd %s ;ttx -t cmap %s';
    private const CONVERT_FILE = '%s/%s.ttx';
    private const CACHE_FILE = '%s/%s.json';

    public function __construct(private HttpClientInterface $client)
    {
        $this->cacheDir = __DIR__;
    }

    public function decodeFont(string $fontName): array
    {
        $url = sprintf(self::URL, $fontName);

        $response = $this->client->request('GET', $url);

        $fontWoff = $fontName . '.woff';
        file_put_contents($this->cacheDir . '/' . $fontWoff, $response->getContent());
        shell_exec(sprintf(self::SHELL_COMMAND, $this->cacheDir, $fontWoff));

        $convertFile = sprintf(self::CONVERT_FILE, $this->cacheDir, $fontName);

        $domDocument = new \DOMDocument();
        $domDocument->load($convertFile);

        $mapElement = $domDocument->getElementsByTagName('map');

        $info = [];

        /** @var \DOMElement $element */
        foreach ($mapElement as $element) {
            $code = ltrim($element->getAttribute('code'), '0');
            $name = $element->getAttribute('name');

            $info[$code] = self::MAP[$name];
        }

        unlink($this->cacheDir . '/' . $fontWoff);
        unlink($convertFile);

        return $info;
    }
}
