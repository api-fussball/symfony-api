<?php declare(strict_types=1);

namespace App\Tests\Unit\Component\FussballDe\Model\MainInfo;

use App\Component\Crawler\Bridge\HttpClientInterface;
use App\Component\Dto\FussballDeRequest;
use App\Component\FussballDe\Font\DecodeProxyInterface;
use App\Component\FussballDe\Model\MainInfo\Games;
use App\Component\FussballDe\Model\MainInfo\GamesCrawler;
use PHPUnit\Framework\TestCase;

class PrevGamesWithNotFountFontTest extends TestCase
{
    public function test()
    {
        $crawlerFaker = $this->createStub(HttpClientInterface::class);
        $crawlerFaker->method('getHtml')
            ->willReturn(file_get_contents(__DIR__ . '/../../../../../_data/prev_games.html'));

        $decodeProxyStub = $this->createStub(DecodeProxyInterface::class);
        $decodeProxyStub->method('decodeFont')
            ->willReturn([1,2,3,4,5]);

        $prevGames = new Games(
            new GamesCrawler(
                $crawlerFaker,
                $decodeProxyStub
            )
        );

        $matchInfo = $prevGames->getPrevClubGames(new FussballDeRequest());

        self::assertCount(10, $matchInfo);

        $firstGame = $matchInfo[0];
        self::assertSame('',$firstGame->homeScore);
        self::assertSame('',$firstGame->awayScore);

        $firstGame = $matchInfo[1];
        self::assertSame('',$firstGame->homeScore);
        self::assertSame('',$firstGame->awayScore);

        $seniorGame = $matchInfo[6];
        self::assertSame('',$seniorGame->homeScore);
        self::assertSame('',$seniorGame->awayScore);

        $unclearScoreGame = $matchInfo[9];
        self::assertSame('',$unclearScoreGame->homeScore);
        self::assertSame('',$unclearScoreGame->awayScore);
    }
}
