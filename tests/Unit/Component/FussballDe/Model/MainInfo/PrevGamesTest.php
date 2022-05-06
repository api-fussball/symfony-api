<?php declare(strict_types=1);

namespace App\Tests\Unit\Component\FussballDe\Model\MainInfo;

use App\Component\Crawler\Bridge\HttpClient;
use App\Component\Crawler\Bridge\HttpClientInterface;
use App\Component\Crawler\CrawlerClient;
use App\Component\Dto\FussballDeRequest;
use App\Component\FussballDe\Font\Decode;
use App\Component\FussballDe\Font\DecodeProxy;
use App\Component\FussballDe\Model\MainInfo\PrevGames;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpClient\CurlHttpClient;

class PrevGamesTest extends TestCase
{
    public function test()
    {
        $crawlerFaker = $this->createStub(HttpClientInterface::class);
        $crawlerFaker->method('getHtml')
            ->willReturn(file_get_contents(__DIR__ . '/../../../../../_data/prev_games.html'));

        $prevGames = new PrevGames(
            $crawlerFaker,
            new DecodeProxy(
                new Decode(
                    new CurlHttpClient(),
                    $this->getParameterBagMock(),
                ),
                $this->getParameterBagMock(),
            )
        );

        $matchInfo = $prevGames->get(new FussballDeRequest());

        self::assertCount(10, $matchInfo);

    }

    /**
     * @return \Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface
     */
    private function getParameterBagMock(): ParameterBagInterface
    {
        return new ParameterBag([
            'kernel.cache_dir' => __DIR__ ,
        ]);
    }
}
