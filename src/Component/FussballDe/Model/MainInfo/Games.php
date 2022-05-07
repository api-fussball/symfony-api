<?php declare(strict_types=1);

namespace App\Component\FussballDe\Model\MainInfo;

use App\Component\Dto\FussballDeRequest;

final class Games implements GamesInterface
{
    private const URL = '/ajax.club.prev.games/-/id/%s/mode/PAGE';

    public function __construct(
        private GamesCrawlerInterface $gamesCrawler
    )
    {
    }

    public function getPrevGames(FussballDeRequest $fussballDeRequest)
    {
        $url = sprintf(
            '/ajax.club.prev.games/-/id/%s/mode/PAGE',
            $fussballDeRequest->id
        );
        return $this->gamesCrawler->get(
            $url
        );
    }

    public function getNextGames(FussballDeRequest $fussballDeRequest)
    {
        $url = sprintf(
            '/ajax.club.next.games/-/id/%s/mode/PAGE',
            $fussballDeRequest->id
        );
        return $this->gamesCrawler->get(
            $url
        );
    }


}
