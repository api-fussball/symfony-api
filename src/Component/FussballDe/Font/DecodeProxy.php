<?php declare(strict_types=1);

namespace App\Component\FussballDe\Font;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

final class DecodeProxy implements DecodeProxyInterface
{
    private const CACHE_FILE = '%s/%s.json';

    private string $cacheDir;

    public function __construct(private DecodeInterface $decode, ParameterBagInterface $parameterBag)
    {
        $this->cacheDir = $parameterBag->get('kernel.cache_dir') . '/fonts';
    }

    public function decodeFont(string $fontName): array
    {
        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir);
        }

        $cacheFile = sprintf(self::CACHE_FILE, $this->cacheDir, $fontName);
        if (!file_exists($cacheFile)) {
            $info = $this->decode->decodeFont($fontName);
            file_put_contents($cacheFile, json_encode($info));
        }

        return json_decode(file_get_contents($cacheFile), true);
    }
}
