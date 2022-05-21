<?php declare(strict_types=1);

namespace App\Component\FussballDe\Model\MainInfo;

use App\Component\Crawler\Bridge\HttpClientInterface;
use App\Component\Dto\ClubMatchInfoTransfer;
use App\Component\FussballDe\Font\DecodeProxyInterface;
use DOMDocument;
use DOMNodeList;
use DOMXPath;
use RuntimeException;

final class GamesCrawler implements GamesCrawlerInterface
{
    private const XPATH = '//*[contains(@class, "%s")]';

    /**
     * @var string[]
     */
    private array $decodeFont = [];

    public function __construct(
        private HttpClientInterface  $crawlerClient,
        private DecodeProxyInterface $decodeProxy,
    )
    {
    }

    /**
     * @param string $url
     *
     * @return \App\Component\Dto\ClubMatchInfoTransfer[]
     */
    public function get(string $url): array
    {
        $html = $this->crawlerClient->getHtml(
            $url
        );

        $dom = new DOMDocument();

        $dom->loadHTML(str_replace('&#', '', $html));

        $clubMatchInfoTransferList = [];

        $clubMatchInfoTransferList = $this->addDateAndCompetitionInfo($dom, $clubMatchInfoTransferList);

        return $this->addScoreInfo($dom, $clubMatchInfoTransferList);
    }

    /**
     * @param \DOMDocument $dom
     * @param \App\Component\Dto\ClubMatchInfoTransfer[] $clubMatchInfoTransferList
     *
     * @return \App\Component\Dto\ClubMatchInfoTransfer[]
     */
    private function addDateAndCompetitionInfo(DOMDocument $dom, array $clubMatchInfoTransferList): array
    {
        $matchDateAndCompetitionInfo = $this->getNodeListByClass($dom, 'visible-small');

        /** @var \DOMElement $info */
        foreach ($matchDateAndCompetitionInfo as $key => $info) {
            $text = $info->nodeValue;
            if (!is_string($text)) {
                continue;
            }

            $nodeValue = trim($text);

            $clubMatchInfoTransfer = new ClubMatchInfoTransfer();

            $dateTimeInfo = strstr($nodeValue, '|', true);

            if ($dateTimeInfo) {
                $clubMatchInfoTransfer->time = substr($dateTimeInfo, -10, 5);
                $clubMatchInfoTransfer->date = substr($dateTimeInfo, -23, 10);
            }

            $text = strstr($nodeValue, '|');
            if ($text) {
                $text = substr($text, 2);
                $competitionInfo = explode(
                    ' | ',
                    $text
                );

                if (count($competitionInfo) === 2) {
                    $clubMatchInfoTransfer->ageGroup = $competitionInfo[0];
                    $clubMatchInfoTransfer->competition = $competitionInfo[1];
                }

                if (count($competitionInfo) === 1) {
                    $clubMatchInfoTransfer->competition = $competitionInfo[0];
                }


                $clubMatchInfoTransferList[$key] = $clubMatchInfoTransfer;
            }

        }

        return $clubMatchInfoTransferList;
    }

    /**
     * @param \DOMDocument $dom
     * @param \App\Component\Dto\ClubMatchInfoTransfer[] $clubMatchInfoTransferList
     *
     * @return \App\Component\Dto\ClubMatchInfoTransfer[]
     */
    private function addScoreInfo(DOMDocument $dom, array $clubMatchInfoTransferList): array
    {
        $matchScore = $this->getNodeListByClass($dom, 'column-score');


        /** @var \DOMElement $info */
        foreach ($matchScore as $key => $info) {
            /** @var \DOMElement $previousElementSibling */
            $previousElementSibling = $info->previousElementSibling;

            /** @var \DOMElement $matchInfo */
            $matchInfo = $previousElementSibling->getElementsByTagName('span')[0];

            $clubMatchInfoTransferList[$key]->awayTeam = utf8_decode($matchInfo->getAttribute('data-alt'));
            $clubMatchInfoTransferList[$key]->awayLogo = 'https:' . $matchInfo->getAttribute('data-responsive-image');

            $matchInfo = $info->previousElementSibling->previousElementSibling->previousElementSibling->getElementsByTagName('span')[0];

            $clubMatchInfoTransferList[$key]->homeTeam = utf8_decode($matchInfo->getAttribute('data-alt'));
            $clubMatchInfoTransferList[$key]->homeLogo = 'https:' . $matchInfo->getAttribute('data-responsive-image');

            $result = trim($info->nodeValue);

            if (str_contains($result, ':')) {

                $fontInfo = $this->getFontInfo($dom);

                $scoreInfo = explode(':', $result);

                $clubMatchInfoTransferList[$key]->homeScore = $this->getScore($scoreInfo[0], $fontInfo);
                $clubMatchInfoTransferList[$key]->awayScore = $this->getScore($scoreInfo[1], $fontInfo);
            }

        }

        return $clubMatchInfoTransferList;
    }

    private function getNodeListByClass(DOMDocument $dom, string $class): DOMNodeList
    {
        $xpath = new DOMXPath($dom);
        $domNodeList = $xpath->query(
            sprintf(self::XPATH, $class),
        );

        if (!$domNodeList instanceof DOMNodeList || $domNodeList->length === 0) {
            throw new RuntimeException('Empty');
        }
        return $domNodeList;
    }

    /**
     * @param string $scoreInfo
     * @param array<string,string> $fontInfo
     *
     * @return string
     */
    private function getScore(string $scoreInfo, array $fontInfo): string
    {
        $scoreHome = array_filter(explode(';', $scoreInfo));

        $finalScore = '';
        foreach ($scoreHome as $score) {
            $score = strtolower($score);
            $info = '-';
            if (isset($fontInfo[$score])) {
                $info = $fontInfo[$score];
            }
            $finalScore .= $info;
        }

        return $finalScore;
    }

    /**
     * @param \DOMDocument $dom
     *
     * @return string[]
     */
    private function getFontInfo(DOMDocument $dom): array
    {
        if (count($this->decodeFont) === 0) {
            $html = $dom->saveHTML();
            if(!is_string($html)) {
                return $this->decodeFont = [];
            }
            $findString = 'data-obfuscation="';
            $pos = strpos($html, $findString);

            if($pos === false) {
                return $this->decodeFont = [];
            }
            $cutHtml = substr($html, $pos + strlen($findString));
            $pos = strpos($cutHtml, '"');

            if($pos === false) {
                return $this->decodeFont = [];
            }

            $decodeFontName = substr($cutHtml, 0, $pos);

            $this->decodeFont = $this->decodeProxy->decodeFont($decodeFontName);
        }
        return $this->decodeFont;
    }
}
