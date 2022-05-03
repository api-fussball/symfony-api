<?php declare(strict_types=1);

namespace App\Component\FussballDe;

use App\Component\Dto\FussballDeRequest;
use App\Component\FussballDe\Font\DecodeInterface;
use App\Component\FussballDe\Model\ClubInfoInterface;

final class FussballDeClient implements FussballDeClientInterface
{
    public function __construct(
        private ClubInfoInterface $clubInfo,
        private DecodeInterface $decode,
    )
    {
    }

    /**
     * @param \App\Component\Dto\FussballDeRequest $fussballDeRequest
     *
     * @return \App\Component\Dto\ClubTeamInfoTransfer[]
     */
    public function clubInfo(FussballDeRequest $fussballDeRequest): array
    {
        return $this->clubInfo->crawler($fussballDeRequest);
    }
}
