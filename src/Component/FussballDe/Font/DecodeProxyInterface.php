<?php declare(strict_types=1);

namespace App\Component\FussballDe\Font;

interface DecodeProxyInterface
{
    public function decodeFont(string $fontName): array;
}
