<?php declare(strict_types=1);

namespace App\Component\FussballDe\Model\MainInfo;

use App\Component\Dto\FussballDeRequest;

interface GamesInterface
{
    public function getPrevGames(FussballDeRequest $fussballDeRequest);

    public function getNextGames(FussballDeRequest $fussballDeRequest);
}
