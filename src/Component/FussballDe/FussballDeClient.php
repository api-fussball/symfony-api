<?php declare(strict_types=1);

namespace App\Component\FussballDe;

use App\Component\Dto\FussballDeRequest;
use App\Component\FussballDe\Model\MainInfo\PrevGamesInterface;
use App\Component\FussballDe\Model\TeamsInfoInterface;

final class FussballDeClient implements FussballDeClientInterface
{
    public function __construct(
        private TeamsInfoInterface $teamsInfo,
        private PrevGamesInterface $prevGames,
    )
    {
    }

    /**
     * @param \App\Component\Dto\FussballDeRequest $fussballDeRequest
     *
     * @return \App\Component\Dto\ClubTeamInfoTransfer[]
     */
    public function teamsInfo(FussballDeRequest $fussballDeRequest): array
    {
        return $this->teamsInfo->crawler($fussballDeRequest);
    }

    /**
     * @param \App\Component\Dto\FussballDeRequest $fussballDeRequest
     *
     * @return \App\Component\Dto\ClubTeamInfoTransfer[]
     */
    public function prevClubGames(FussballDeRequest $fussballDeRequest): array
    {
        return $this->prevGames->get($fussballDeRequest);
    }



}
