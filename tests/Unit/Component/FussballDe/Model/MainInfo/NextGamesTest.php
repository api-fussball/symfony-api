<?php declare(strict_types=1);

namespace App\Tests\Unit\Component\FussballDe\Model\MainInfo;

use App\Component\Crawler\Bridge\HttpClientInterface;
use App\Component\Dto\FussballDeRequest;
use App\Component\FussballDe\Font\DecodeProxyInterface;
use App\Component\FussballDe\Model\MainInfo\Games;
use App\Component\FussballDe\Model\MainInfo\GamesCrawler;
use PHPUnit\Framework\TestCase;

class NextGamesTest extends TestCase
{
    public function test()
    {
        $crawlerFaker = $this->createStub(HttpClientInterface::class);
        $crawlerFaker->method('getHtml')
            ->willReturn(file_get_contents(__DIR__ . '/../../../../../_data/next_games.html'));

        $decodeProxyStub = $this->createStub(DecodeProxyInterface::class);
        $decodeProxyStub->method('decodeFont')
            ->willReturn(json_decode(file_get_contents(__DIR__ . '/33dfshl1.json'), true));

        $prevGames = new Games(
            new GamesCrawler(
                $crawlerFaker,
                $decodeProxyStub
            )
        );

        $matchInfo = $prevGames->getPrevGames(new FussballDeRequest());

        self::assertCount(10, $matchInfo);

        $firstGame = $matchInfo[0];
        self::assertSame('-',$firstGame->homeScore);
        self::assertSame('-',$firstGame->awayScore);
        self::assertSame('https://www.fussball.de/export.media/-/action/getLogo/format/3/id/00ES8GN91400002IVV0AG08LVUPGND5I',$firstGame->homeLogo);
        self::assertSame('https://www.fussball.de/export.media/-/action/getLogo/format/3/id/00ES8GN91400001GVV0AG08LVUPGND5I',$firstGame->awayLogo);
        self::assertSame('1.Kreisklasse',$firstGame->competition);
        self::assertSame('D-Junioren',$firstGame->ageGroup);
        self::assertSame('Dellbrück U12 II',$firstGame->awayTeam);
        self::assertSame('Fühlingen U12',$firstGame->homeTeam);
        self::assertSame('08.05.2022',$firstGame->date);
        self::assertSame('10:00',$firstGame->time);

        $fJuniorGame = $matchInfo[6];
        self::assertSame('',$fJuniorGame->homeScore);
        self::assertSame('',$fJuniorGame->awayScore);
        self::assertSame('https://www.fussball.de/export.media/-/action/getLogo/format/3/id/00ES8GN91400002IVV0AG08LVUPGND5I',$fJuniorGame->homeLogo);
        self::assertSame('https://www.fussball.de/export.media/-/action/getLogo/format/3/id/00ES8GN91400002FVV0AG08LVUPGND5I',$fJuniorGame->awayLogo);
        self::assertSame('Kreisfreundschaftsspiele',$fJuniorGame->competition);
        self::assertSame('F-Junioren',$fJuniorGame->ageGroup);
        self::assertSame('Fortuna Köln U9',$fJuniorGame->awayTeam);
        self::assertSame('Fühlingen U9',$fJuniorGame->homeTeam);
        self::assertSame('08.05.2022',$fJuniorGame->date);
        self::assertSame('16:15',$fJuniorGame->time);
    }
}
