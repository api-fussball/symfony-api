<?php declare(strict_types=1);

namespace App\Component\FussballDe;

use App\Component\Dto\FussballDeRequest;
use App\Component\FussballDe\Model\MainInfo\GamesInterface;
use App\Component\FussballDe\Model\TeamsInfoInterface;

final class FussballDeClient implements FussballDeClientInterface
{
    public function __construct(
        private TeamsInfoInterface $teamsInfo,
        private GamesInterface $gamesCrawler,
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
     * @return \App\Component\Dto\ClubMatchInfoTransfer[]
     */
    public function prevClubGames(FussballDeRequest $fussballDeRequest): array
    {
        return $this->gamesCrawler->getPrevGames($fussballDeRequest);
    }

    /**
     * @param \App\Component\Dto\FussballDeRequest $fussballDeRequest
     *
     * @return \App\Component\Dto\ClubMatchInfoTransfer[]
     */
    public function nextClubGames(FussballDeRequest $fussballDeRequest): array
    {
        return $this->gamesCrawler->getNextGames($fussballDeRequest);
    }

}
