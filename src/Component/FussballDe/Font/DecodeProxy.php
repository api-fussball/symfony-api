<?php declare(strict_types=1);

namespace App\Component\FussballDe\Font;

use RuntimeException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

final class DecodeProxy implements DecodeProxyInterface
{
    private const CACHE_FILE = '%s/%s.json';

    private string $cacheDir;

    public function __construct(private DecodeInterface $decode, ParameterBagInterface $parameterBag)
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

        $cacheFile = sprintf(self::CACHE_FILE, $this->cacheDir, $fontName);
        if (!file_exists($cacheFile)) {
            $info = $this->decode->decodeFont($fontName);
            file_put_contents($cacheFile, json_encode($info));

            return $info;
        }

        $cacheContent = file_get_contents($cacheFile);
        if($cacheContent === false) {
            return [];
            //throw new RuntimeException('Font not found');
        }

        try {
            return json_decode($cacheContent, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            return [];
        }
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
