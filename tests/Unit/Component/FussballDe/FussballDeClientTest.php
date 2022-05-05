<?php declare(strict_types=1);

namespace App\Tests\Unit\Component\FussballDe;

use App\Component\Crawler\Bridge\HttpClientInterface;
use App\Component\Crawler\CrawlerClient;
use App\Component\Dto\FussballDeRequest;
use App\Component\FussballDe\Model\TeamsInfo;
use PHPUnit\Framework\TestCase;

final class FussballDeClientTest extends TestCase
{
    public function testClubInfo()
    {
        $crowlerFaker = $this->createStub(HttpClientInterface::class);

        $crowlerFaker->method('getHtml')
            ->willReturn(file_get_contents(__DIR__ . '/../../../_data/club_info.html'));

        $fussballDeClinet = new TeamsInfo(new CrawlerClient($crowlerFaker));

        $info = $fussballDeClinet->crawler(new FussballDeRequest);

        self::assertCount(12, $info);

        $firstSeniorTeam = $info[0];

        self::assertSame('Herren - Fühlingen I', $firstSeniorTeam->name);
        self::assertSame(
            '/mannschaft/sv-fuehlingen-sv-fuehlingen-chorweiler-e-v-mittelrhein/-/saison/2122/team-id/011MIC9NDS000000VTVG0001VTR8C1K7',
            $firstSeniorTeam->url
        );

        $u14Team = $info[5];

        self::assertSame('C-Junioren - Fühlingen U14', $u14Team->name);
        self::assertSame(
            '/mannschaft/sv-fuehlingen-u14-sv-fuehlingen-chorweiler-e-v-mittelrhein/-/saison/2122/team-id/011MIAD9JG000000VTVG0001VTR8C1K7',
            $u14Team->url
        );
    }
}
