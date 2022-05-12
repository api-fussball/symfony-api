<?php declare(strict_types=1);

namespace App\Component\FussballDe\Model;

use App\Component\Crawler\Bridge\HttpClientInterface;
use App\Component\Dto\FussballDeRequest;
use App\Component\Dto\TeamTableTransfer;
use DOMDocument;
use DOMNodeList;

final class TableResult
{
    private const URL = '/ajax.team.table/-/team-id/%s';

    public function __construct(
        private HttpClientInterface $crawlerClient,
    )
    {
    }

    /**
     * @param \App\Component\Dto\FussballDeRequest $fussballDeRequest
     *
     * @return \App\Component\Dto\TeamTableTransfer[]
     */
    public function get(FussballDeRequest $fussballDeRequest): array
    {
        $html = $this->getHtml($fussballDeRequest);

        $dom = new DOMDocument();
        $dom->loadHTML($html);

        /** @var \DOMElement $domElement */
        $domElement = $dom->getElementsByTagName('tbody')[0];

        /** @var DOMNodeList $tableInfos */
        $tableInfos = $domElement->getElementsByTagName('tr');

        $tableTeamInfoList = [];
        /** @var \DOMElement $childNode */
        foreach ($tableInfos as $childNode) {

            $tableTeamInfo = new TeamTableTransfer();

            $this->addRelegationAndPromotionInfo($childNode, $tableTeamInfo);

            $trInfoList = $childNode->getElementsByTagName('td');

            $tableTeamInfo->place = (int)$trInfoList[1]->nodeValue;

            /** @var \DOMElement $clubColumn */
            $clubColumn = $trInfoList[2];

            $tableTeamInfo->team = utf8_decode(trim($clubColumn->nodeValue));

            $tableTeamInfo->img = 'https:' . $clubColumn->getElementsByTagName('img')[0]->getAttribute('src');

            $tableTeamInfo->games = (int)$trInfoList[3]->nodeValue;
            $tableTeamInfo->won = (int)$trInfoList[4]->nodeValue;
            $tableTeamInfo->draw = (int)$trInfoList[5]->nodeValue;
            $tableTeamInfo->lost = (int)$trInfoList[6]->nodeValue;
            $tableTeamInfo->goal = trim($trInfoList[7]->nodeValue);
            $tableTeamInfo->goalDifference = (int)$trInfoList[8]->nodeValue;
            $tableTeamInfo->points = (int)$trInfoList[9]->nodeValue;

            $tableTeamInfoList[] = $tableTeamInfo;
        }

        return $tableTeamInfoList;
    }

    private function addRelegationAndPromotionInfo(\DOMElement $childNode, TeamTableTransfer $tableTeamInfo): void
    {
        $className = $childNode->getAttribute('class');

        if (str_contains($className, 'relegation')) {
            $tableTeamInfo->isRelegation = true;
        }

        if (str_contains($className, 'promotion')) {
            $tableTeamInfo->isPromotion = true;
        }
    }

    private function getHtml(FussballDeRequest $fussballDeRequest): string
    {
        $url = sprintf(
            self::URL,
            $fussballDeRequest->id
        );

        return $this->crawlerClient->getHtml(
            $url
        );
    }
}
