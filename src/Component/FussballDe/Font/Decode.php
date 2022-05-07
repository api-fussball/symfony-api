<?php declare(strict_types=1);

namespace App\Component\FussballDe\Font;

use DOMDocument;
use RuntimeException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
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
    private const SHELL_COMMAND = 'cd %s ;ttx -t cmap %s >/dev/null 2>&1';
    private const CONVERT_FILE = '%s/%s.ttx';
    private const FONT_FILE_PATH = '%s/%s.woff';

    private string $cacheDir;

    public function __construct(private HttpClientInterface $client, ParameterBagInterface $parameterBag)
    {
        $this->cacheDir = $this->getCacheDir($parameterBag);
    }

    /**
     * @param string $fontName
     *
     * @return string[]
     */
    public function decodeFont(string $fontName): array
    {
        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir);
        }

        $url = sprintf(self::URL, $fontName);

        $response = $this->client->request('GET', $url);

        $fontWoff = sprintf(self::FONT_FILE_PATH, $this->cacheDir, $fontName);
        file_put_contents($fontWoff, $response->getContent());

        /** @psalm-suppress ForbiddenCode */
        shell_exec(sprintf(self::SHELL_COMMAND, $this->cacheDir, $fontWoff));

        $convertFile = sprintf(self::CONVERT_FILE, $this->cacheDir, $fontName);

        $domDocument = new DOMDocument();
        $domDocument->load($convertFile);

        $mapElement = $domDocument->getElementsByTagName('map');

        $info = [];

        /** @var \DOMElement $element */
        foreach ($mapElement as $element) {
            $code = ltrim($element->getAttribute('code'), '0');
            $name = $element->getAttribute('name');

            $info[$code] = self::MAP[$name];
        }

        unlink($fontWoff);
        unlink($convertFile);

        return $info;
    }

    /**
     * @param \Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface $parameterBag
     *
     * @return string
     */
    private function getCacheDir(ParameterBagInterface $parameterBag): string
    {
        $cacheDir = $parameterBag->get('kernel.cache_dir');
        if (is_string($cacheDir)) {
            return $cacheDir . '/fonts';
        }

        throw new RuntimeException('CacheDir not found');
    }
}
