<?php declare(strict_types=1);

namespace App\Component\FussballDe\Model;

use App\Component\Dto\FussballDeRequest;

interface TableResultInterface
{
    /**
     * @param \App\Component\Dto\FussballDeRequest $fussballDeRequest
     *
     * @return \App\Component\Dto\TeamTableTransfer[]
     */
    public function get(FussballDeRequest $fussballDeRequest): array;
}
