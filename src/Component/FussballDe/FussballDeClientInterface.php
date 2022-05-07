<?php declare(strict_types=1);

namespace App\Component\FussballDe;

use App\Component\Dto\FussballDeRequest;

interface FussballDeClientInterface
{
    /**
     * @param \App\Component\Dto\FussballDeRequest $fussballDeRequest
     *
     * @return \App\Component\Dto\ClubTeamInfoTransfer[]
     */
    public function teamsInfo(FussballDeRequest $fussballDeRequest): array;

    /**
     * @param \App\Component\Dto\FussballDeRequest $fussballDeRequest
     *
     * @return \App\Component\Dto\ClubMatchInfoTransfer[]
     */
    public function prevClubGames(FussballDeRequest $fussballDeRequest): array;

    /**
     * @param \App\Component\Dto\FussballDeRequest $fussballDeRequest
     *
     * @return \App\Component\Dto\ClubMatchInfoTransfer[]
     */
    public function nextClubGames(FussballDeRequest $fussballDeRequest): array;
}
