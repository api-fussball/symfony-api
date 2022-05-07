<?php declare(strict_types=1);

namespace App\Component\FussballDe\Model\MainInfo;

use App\Component\Dto\FussballDeRequest;

final class Games implements GamesInterface
{
    public function __construct(
        private GamesCrawlerInterface $gamesCrawler
    )
    {
    }

    /**
     * @param \App\Component\Dto\FussballDeRequest $fussballDeRequest
     *
     * @return \App\Component\Dto\ClubMatchInfoTransfer[]
     */
    public function getPrevGames(FussballDeRequest $fussballDeRequest): array
    {
        $url = sprintf(
            '/ajax.club.prev.games/-/id/%s/mode/PAGE',
            $fussballDeRequest->id
        );
        return $this->gamesCrawler->get(
            $url
        );
    }

    /**
     * @param \App\Component\Dto\FussballDeRequest $fussballDeRequest
     *
     * @return \App\Component\Dto\ClubMatchInfoTransfer[]
     */
    public function getNextGames(FussballDeRequest $fussballDeRequest): array
    {
        $url = sprintf(
            '/ajax.club.next.games/-/id/%s/mode/PAGE',
            $fussballDeRequest->id
        );
        return $this->gamesCrawler->get(
            $url
        );
    }

    /**
     * @param \App\Component\Dto\FussballDeRequest $fussballDeRequest
     *
     * @return \App\Component\Dto\ClubMatchInfoTransfer[]
     */
    public function getPrevTeamGames(FussballDeRequest $fussballDeRequest): array
    {
        $url = sprintf(
            '/ajax.team.prev.games/-/mode/PAGE/team-id/%s',
            $fussballDeRequest->id
        );
        return $this->gamesCrawler->get(
            $url
        );
    }

    /**
     * @param \App\Component\Dto\FussballDeRequest $fussballDeRequest
     *
     * @return \App\Component\Dto\ClubMatchInfoTransfer[]
     */
    public function getNextTeamGames(FussballDeRequest $fussballDeRequest): array
    {
        $url = sprintf(
            '/ajax.team.next.games/-/mode/PAGE/team-id/%s',
            $fussballDeRequest->id
        );
        return $this->gamesCrawler->get(
            $url
        );
    }

}
