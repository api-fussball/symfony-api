<?php declare(strict_types=1);

namespace App\Component\FussballDe\Model\MainInfo;

use App\Component\Dto\FussballDeRequest;

interface GamesInterface
{
    /**
     * @param \App\Component\Dto\FussballDeRequest $fussballDeRequest
     *
     * @return \App\Component\Dto\ClubMatchInfoTransfer[]
     */
    public function getPrevGames(FussballDeRequest $fussballDeRequest): array;

    /**
     * @param \App\Component\Dto\FussballDeRequest $fussballDeRequest
     *
     * @return \App\Component\Dto\ClubMatchInfoTransfer[]
     */
    public function getNextGames(FussballDeRequest $fussballDeRequest): array;

    /**
     * @param \App\Component\Dto\FussballDeRequest $fussballDeRequest
     *
     * @return \App\Component\Dto\ClubMatchInfoTransfer[]
     */
    public function getNextTeamGames(FussballDeRequest $fussballDeRequest): array;

    /**
     * @param \App\Component\Dto\FussballDeRequest $fussballDeRequest
     *
     * @return \App\Component\Dto\ClubMatchInfoTransfer[]
     */
    public function getPrevTeamGames(FussballDeRequest $fussballDeRequest): array;
}
