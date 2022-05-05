<?php declare(strict_types=1);

namespace App\Component\FussballDe\Model;

use App\Component\Dto\FussballDeRequest;

interface TeamsInfoInterface
{
    /**
     * @return \App\Component\Dto\ClubTeamInfoTransfer[]
     */
    public function crawler(FussballDeRequest $fussballDeRequest): array;
}
