<?php declare(strict_types=1);

namespace App\Tests\Api\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApiTest extends WebTestCase
{
    private KernelBrowser $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = static::createClient();
    }

    public function testClubInfo()
    {
        $this->client->request('GET', '/api/club/00ES8GN91400002IVV0AG08LVUPGND5I');

        self::assertResponseStatusCodeSame(200);

        $response = $this->client->getResponse();

        self::assertTrue($response->headers->contains('Content-Type', 'application/json'));

        $responseRequest = json_decode($response->getContent(), true);

        self::assertArrayHasKey('data', $responseRequest);
        self::assertSame('Herren - Fühlingen I', $responseRequest['data'][0]['name']);


        $clubs = $responseRequest['data'];
        foreach ($clubs as $club) {
            $info = explode('/', $club['fussballDeUrl']);
            $id = end($info);
            self::assertSame('/club/next_games/' . $id, $club['urls']['nextGames']);
            self::assertSame('/club/prev_games/' . $id, $club['urls']['prevGames']);
            self::assertSame('/club/table/' . $id, $club['urls']['table']);
        }
    }

    public function testPrevTeamGames()
    {
        $this->client->request('GET', '/api/team/prev_games/011MIC9NDS000000VTVG0001VTR8C1K7#!');

        $data = $this->getDataFromRequest();
        self::assertGreaterThan(0, $data);

        $this->checkDate($data);
        $this->checkTime($data);
        $this->checkTeam($data, 'Fühlingen I');

        $score = $this->getScore($data);
        self::assertGreaterThan(0, $score);
    }

    public function testNextTeamGames()
    {
        $this->client->request('GET', '/api/team/next_games/011MIC9NDS000000VTVG0001VTR8C1K7');

        $data = $this->getDataFromRequest();
        self::assertGreaterThan(0, $data);

        $this->checkDate($data);
        $this->checkTime($data);
        $this->checkTeam($data, 'Fühlingen I');

        $score = $this->getScore($data);
        self::assertSame(0, $score);
    }

    public function testPrevGames()
    {
        $this->client->request('GET', '/api/club/prev_games/00ES8GN91400002IVV0AG08LVUPGND5I');

        $data = $this->getDataFromRequest();
        self::assertCount(10, $data);

        $this->checkDate($data);
        $this->checkTime($data);
        $this->checkTeam($data);

        $score = $this->getScore($data);
        self::assertGreaterThan(0, $score);
    }

    public function testNextGames()
    {
        $this->client->request('GET', '/api/club/next_games/00ES8GN91400002IVV0AG08LVUPGND5I');

        $data = $this->getDataFromRequest();
        //self::assertCount(10, $data);

        $this->checkDate($data);
        $this->checkTime($data);
        $this->checkTeam($data);

        $score = $this->getScore($data);
        self::assertSame(0, $score);
    }

    public function testTable()
    {
        $this->client->request('GET', '/api/team/table/011MIC9NDS000000VTVG0001VTR8C1K7');

        $data = $this->getDataFromRequest();


        $team = $data[0];
        self::assertTrue($team['isPromotion']);
        self::assertFalse($team['isRelegation']);
        self::assertGreaterThan(0, $team['games']);

        $month = (int)date('m');

        if ($month !== 7) {
            self::assertGreaterThan(0, $team['goal']);
            self::assertGreaterThan(0, $team['points']);
            self::assertGreaterThan(0, $team['goalDifference']);
        }

        self::assertSame(1, $team['place']);

        $team = end($data);
        self::assertTrue($team['isRelegation']);
        self::assertFalse($team['isPromotion']);
    }

    private function getScore(array $data): int
    {
        $score = 0;

        foreach ($data as $info) {
            $score += (int)$info['homeScore'];
            $score += (int)$info['awayScore'];
        }

        return $score;
    }

    private function checkTeam(array $data, string $expectedTeam = 'Fühlingen I'): void
    {
        $teams = [];

        foreach ($data as $info) {
            $teams[] = $info['homeTeam'];
            $teams[] = $info['awayTeam'];
        }

        $teams = array_unique($teams);
        self::assertTrue(in_array($expectedTeam, $teams));
    }

    private function checkDate(array $data): void
    {
        foreach ($data as $info) {
            self::assertArrayHasKey('date', $info);
            $date = $info['date'];
            self::assertSame(10, strlen($date));

            $dateInfo = explode('.', $date);
            self::assertTrue(
                checkdate((int)$dateInfo[1], (int)$dateInfo[0], (int)$dateInfo[2])
            );
        }
    }

    private function checkTime(array $data): void
    {
        foreach ($data as $info) {
            self::assertArrayHasKey('time', $info);
            $time = $info['time'];

            self::assertSame(5, strlen($time));
            self::assertNotFalse(strtotime($time));
        }
    }

    private function getDataFromRequest(): array
    {
        self::assertResponseStatusCodeSame(200);

        $response = $this->client->getResponse();

        self::assertTrue($response->headers->contains('Content-Type', 'application/json'));

        $responseRequest = json_decode($response->getContent(), true);

        self::assertArrayHasKey('data', $responseRequest);

        return $responseRequest['data'];
    }
}
