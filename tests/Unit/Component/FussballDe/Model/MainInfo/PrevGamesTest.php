<?php declare(strict_types=1);

namespace App\Tests\Unit\Component\FussballDe\Model\MainInfo;

use App\Component\Crawler\Bridge\HttpClientInterface;
use App\Component\Dto\FussballDeRequest;
use App\Component\FussballDe\Font\DecodeProxyInterface;
use App\Component\FussballDe\Model\MainInfo\Games;
use App\Component\FussballDe\Model\MainInfo\GamesCrawler;
use PHPUnit\Framework\TestCase;

class PrevGamesTest extends TestCase
{
    public function test()
    {
        $crawlerFaker = $this->createStub(HttpClientInterface::class);
        $crawlerFaker->method('getHtml')
            ->willReturn(file_get_contents(__DIR__ . '/../../../../../_data/prev_games.html'));

        $decodeProxyStub = $this->createStub(DecodeProxyInterface::class);
        $decodeProxyStub->method('decodeFont')
            ->willReturn(json_decode(file_get_contents(__DIR__ . '/ap6umsuq.json'), true));

        $prevGames = new Games(
            new GamesCrawler(
                $crawlerFaker,
                $decodeProxyStub
            )
        );

        $matchInfo = $prevGames->getPrevGames(new FussballDeRequest());

        self::assertCount(10, $matchInfo);

        $firstGame = $matchInfo[0];
        self::assertSame('0',$firstGame->homeScore);
        self::assertSame('1',$firstGame->awayScore);
        self::assertSame('https://www.fussball.de/export.media/-/action/getLogo/format/3/id/00ES8GN91400002IVV0AG08LVUPGND5I',$firstGame->homeLogo);
        self::assertSame('https://www.fussball.de/export.media/-/action/getLogo/format/3/id/00ES8GN91400003QVV0AG08LVUPGND5I',$firstGame->awayLogo);
        self::assertSame('1.Kreisklasse',$firstGame->competition);
        self::assertSame('E-Junioren',$firstGame->ageGroup);
        self::assertSame('1. JFS Köln U10 II',$firstGame->awayTeam);
        self::assertSame('Fühlingen U10',$firstGame->homeTeam);
        self::assertSame('30.04.2022',$firstGame->date);
        self::assertSame('13:45',$firstGame->time);

        $seniorGame = $matchInfo[6];
        self::assertSame('14',$seniorGame->homeScore);
        self::assertSame('0',$seniorGame->awayScore);
        self::assertSame('https://www.fussball.de/export.media/-/action/getLogo/format/3/id/00ES8GN91400005EVV0AG08LVUPGND5I',$seniorGame->homeLogo);
        self::assertSame('https://www.fussball.de/export.media/-/action/getLogo/format/3/id/00ES8GN91400002IVV0AG08LVUPGND5I',$seniorGame->awayLogo);
        self::assertSame('Kreisliga D',$seniorGame->competition);
        self::assertSame('Herren',$seniorGame->ageGroup);
        self::assertSame('Fühlingen II',$seniorGame->awayTeam);
        self::assertSame('Vingst 05 II',$seniorGame->homeTeam);
        self::assertSame('01.05.2022',$seniorGame->date);
        self::assertSame('13:00',$seniorGame->time);
    }
}
