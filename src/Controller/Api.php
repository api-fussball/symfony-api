<?php declare(strict_types=1);

namespace App\Controller;

use App\Component\Dto\FussballDeRequest;
use App\Component\FussballDe\FussballDeClientInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api")
 */
class Api
{
    public function __construct(
        private FussballDeClientInterface $fussballDeClient,
    )
    {
    }

    /**
     * @Route("/club/{id}", name="api_club")
     */
    public function club(string $id): JsonResponse
    {
        $clubInfoTransfer = new FussballDeRequest();
        $clubInfoTransfer->id = $id;

        $clubInfoTransferList = $this->fussballDeClient->clubInfo($clubInfoTransfer);

        return new JsonResponse(['data' => $clubInfoTransferList]);
    }
}
