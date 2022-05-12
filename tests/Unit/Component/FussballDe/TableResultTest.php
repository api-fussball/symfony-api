<?php declare(strict_types=1);

namespace App\Tests\Unit\Component\FussballDe;

use App\Component\Crawler\Bridge\HttpClientInterface;
use App\Component\Dto\FussballDeRequest;
use App\Component\FussballDe\Model\TableResult;
use PHPUnit\Framework\TestCase;

class TableResultTest extends TestCase
{
    public function test()
    {
        $crawlerFaker = $this->createStub(HttpClientInterface::class);
        $crawlerFaker->method('getHtml')
            ->willReturn(file_get_contents(__DIR__ . '/../../../_data/team_table.html'));

        $tableResult = new TableResult(
            $crawlerFaker
        );

        $table = $tableResult->get(new FussballDeRequest());

        self::assertCount(11, $table);

        $team = $table[0];
        self::assertSame('Nippes 78 U14',$team->team);
        self::assertSame(
            'https://www.fussball.de/export.media/-/action/getLogo/format/3/id/00ES8GN91400003TVV0AG08LVUPGND5I/verband/0123456789ABCDEF0123456700004120',
            $team->img
        );
        self::assertSame(1, $team->place);
        self::assertSame(18, $team->games);
        self::assertSame(14, $team->won);
        self::assertSame(2, $team->draw);
        self::assertSame(2, $team->lost);
        self::assertSame('93 : 18', $team->goal);
        self::assertSame(75, $team->goalDifference);
        self::assertSame(44, $team->points);
        self::assertSame(true, $team->isPromotion);
        self::assertSame(false, $team->isRelegation);

        $team = $table[7];
        self::assertSame('Rath-Heumar U14',$team->team);
        self::assertSame(
            'https://www.fussball.de/export.media/-/action/getLogo/format/3/id/00ES8GN91400004FVV0AG08LVUPGND5I/verband/0123456789ABCDEF0123456700004120',
            $team->img
        );
        self::assertSame(8, $team->place);
        self::assertSame(19, $team->games);
        self::assertSame(4, $team->won);
        self::assertSame(1, $team->draw);
        self::assertSame(14, $team->lost);
        self::assertSame('35 : 86', $team->goal);
        self::assertSame(-51, $team->goalDifference);
        self::assertSame(13, $team->points);
        self::assertSame(false, $team->isPromotion);
        self::assertSame(false, $team->isRelegation);

        $team = $table[10];
        self::assertSame('Fühlingen U14',$team->team);
        self::assertSame(
            'https://www.fussball.de/export.media/-/action/getLogo/format/3/id/00ES8GN91400002IVV0AG08LVUPGND5I/verband/0123456789ABCDEF0123456700004120',
            $team->img
        );
        self::assertSame(11, $team->place);
        self::assertSame(18, $team->games);
        self::assertSame(1, $team->won);
        self::assertSame(1, $team->draw);
        self::assertSame(16, $team->lost);
        self::assertSame('12 : 113', $team->goal);
        self::assertSame(-101, $team->goalDifference);
        self::assertSame(4, $team->points);
        self::assertSame(false, $team->isPromotion);
        self::assertSame(true, $team->isRelegation);
    }
}
