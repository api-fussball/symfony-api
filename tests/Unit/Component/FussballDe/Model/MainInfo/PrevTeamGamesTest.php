<?php declare(strict_types=1);

namespace App\Tests\Unit\Component\FussballDe\Model\MainInfo;

use App\Component\Crawler\Bridge\HttpClientInterface;
use App\Component\Dto\FussballDeRequest;
use App\Component\FussballDe\Font\DecodeProxyInterface;
use App\Component\FussballDe\Model\MainInfo\Games;
use App\Component\FussballDe\Model\MainInfo\GamesCrawler;
use PHPUnit\Framework\TestCase;

class PrevTeamGamesTest extends TestCase
{
    public function test()
    {
        $crawlerFaker = $this->createStub(HttpClientInterface::class);
        $crawlerFaker->method('getHtml')
            ->willReturn(file_get_contents(__DIR__ . '/../../../../../_data/prev_team_games.html'));

        $decodeProxyStub = $this->createStub(DecodeProxyInterface::class);
        $decodeProxyStub->method('decodeFont')
            ->willReturn(json_decode(file_get_contents(__DIR__ . '/mbxdus9j.json'), true));

        $prevGames = new Games(
            new GamesCrawler(
                $crawlerFaker,
                $decodeProxyStub
            )
        );

        $matchInfo = $prevGames->getPrevClubGames(new FussballDeRequest());

        self::assertCount(10, $matchInfo);

        $firstGame = $matchInfo[0];

        self::assertSame('3',$firstGame->homeScore);
        self::assertSame('3',$firstGame->awayScore);
        self::assertSame('https://www.fussball.de/export.media/-/action/getLogo/format/3/id/01SE6BI47C000000VS548985VV2TPI9R',$firstGame->homeLogo);
        self::assertSame('https://www.fussball.de/export.media/-/action/getLogo/format/3/id/00ES8GN91400002IVV0AG08LVUPGND5I',$firstGame->awayLogo);
        self::assertSame('Kreisliga C',$firstGame->competition);
        self::assertEmpty($firstGame->ageGroup);
        self::assertSame('Fühlingen I',$firstGame->awayTeam);
        self::assertSame('Galatasaray I',$firstGame->homeTeam);
        self::assertSame('08.05.2022',$firstGame->date);
        self::assertSame('15:00',$firstGame->time);

        $seniorGame = $matchInfo[2];
        self::assertSame('5',$seniorGame->homeScore);
        self::assertSame('4',$seniorGame->awayScore);
        self::assertSame('https://www.fussball.de/export.media/-/action/getLogo/format/3/id/00ES8GN914000051VV0AG08LVUPGND5I',$seniorGame->homeLogo);
        self::assertSame('https://www.fussball.de/export.media/-/action/getLogo/format/3/id/00ES8GN91400002IVV0AG08LVUPGND5I',$seniorGame->awayLogo);
        self::assertSame('Kreisliga C',$seniorGame->competition);
        self::assertEmpty($seniorGame->ageGroup);
        self::assertSame('Fühlingen I',$seniorGame->awayTeam);
        self::assertSame('TPSK I',$seniorGame->homeTeam);
        self::assertSame('24.04.2022',$seniorGame->date);
        self::assertSame('15:00',$seniorGame->time);
    }
}
