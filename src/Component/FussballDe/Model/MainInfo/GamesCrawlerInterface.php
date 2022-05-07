<?php declare(strict_types=1);

namespace App\Component\FussballDe\Model\MainInfo;

interface GamesCrawlerInterface
{
    /**
     * @param string $url
     *
     * @return \App\Component\Dto\ClubMatchInfoTransfer[]
     */
    public function get(string $url): array;
}
