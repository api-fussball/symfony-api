<?php declare(strict_types=1);

namespace App\Component\FussballDe\Model;

use App\Component\Dto\FussballDeRequest;

interface ClubInfoInterface
{
    /**
     * @return \App\Component\Dto\ClubTeamInfoTransfer[]
     */
    public function crawler(FussballDeRequest $fussballDeRequest): array;
}
