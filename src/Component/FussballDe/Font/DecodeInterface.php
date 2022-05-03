<?php declare(strict_types=1);

namespace App\Component\FussballDe\Font;

interface DecodeInterface
{
    public function decodeFont(string $fontName): array;
}
