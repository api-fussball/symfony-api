<?php declare(strict_types=1);

namespace App\Component\FussballDe\Font;

interface DecodeInterface
{
    /**
     * @param string $fontName
     *
     * @return string[]
     */
    public function decodeFont(string $fontName): array;
}
