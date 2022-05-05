<?php declare(strict_types=1);

namespace App\Component\FussballDe\Model;

use App\Component\Crawler\CrawlerClientInterface;
use App\Component\Dto\ClubTeamInfoTransfer;
use App\Component\Dto\FussballDeRequest;

final class TeamsInfo implements TeamsInfoInterface
{

    private const URL = '/ajax.club.teams/-/action/search/id/%s/saison/2122/mannschaftsart/-1/wettkampftyp/-1';
    private const XPATH = './/div[contains(concat(" ",normalize-space(@class)," ")," result ")]//div[contains(concat(" ",normalize-space(@class)," ")," item ")]';

    public function __construct(
        private CrawlerClientInterface $crawlerClient,
    )
    {
    }

    /**
     * @return \App\Component\Dto\ClubTeamInfoTransfer[]
     */
    public function crawler(FussballDeRequest $fussballDeRequest): array
    {
        $teamsInfo = $this->crawlerClient->get(
            $this->getUrl($fussballDeRequest),
            self::XPATH
        );

        $clubInfo = [];

        /** @var \DOMElement $team */
        foreach ($teamsInfo as $team) {
            /** @var \DOMElement $teamHtmlInfo */
            $teamHtmlInfo = $team->getElementsByTagName('h4')[0];

            /** @var \DOMElement $firstElement */
            $firstElement = $teamHtmlInfo->firstElementChild;
            $url = $firstElement->getAttribute('href');

            if (empty($url) || empty($teamHtmlInfo->nodeValue)) {
                continue;
            }

            $clubInfoTransfer = new ClubTeamInfoTransfer();
            $clubInfoTransfer->url = str_replace('https://www.fussball.de', '', $url);
            $clubInfoTransfer->name = utf8_decode(trim($teamHtmlInfo->nodeValue));

            $clubInfo[] = $clubInfoTransfer;
        }

        return $clubInfo;
    }

    private function getUrl(FussballDeRequest $fussballDeRequest): string
    {
        return sprintf(
            self::URL,
            $fussballDeRequest->id
        );
    }
}
