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
        $clubInfoTransferList = $this->fussballDeClient->teamsInfo(
            $this->getFussballDeRequest($id)
        );

        return new JsonResponse(['data' => $clubInfoTransferList]);
    }

    /**
     * @Route("/club/prev_games/{id}", name="api_club_prev_games")
     */
    public function clubPrevGames(string $id): JsonResponse
    {
        $clubInfoTransferList = $this->fussballDeClient->prevClubGames(
            $this->getFussballDeRequest($id)
        );

        return new JsonResponse(['data' => $clubInfoTransferList]);
    }

    /**
     * @Route("/club/next_games/{id}", name="api_club_next_games")
     */
    public function clubNextGames(string $id): JsonResponse
    {
        $clubInfoTransferList = $this->fussballDeClient->nextClubGames(
            $this->getFussballDeRequest($id)
        );

        return new JsonResponse(['data' => $clubInfoTransferList]);
    }

    /**
     * @Route("/team/prev_games/{id}", name="api_team_prev_games")
     */
    public function teamPrevGames(string $id): JsonResponse
    {
        $teamInfoTransferList = $this->fussballDeClient->prevTeamGames(
            $this->getFussballDeRequest($id)
        );

        return new JsonResponse(['data' => $teamInfoTransferList]);
    }

    /**
     * @Route("/team/next_games/{id}", name="api_team_next_games")
     */
    public function teamNextGames(string $id): JsonResponse
    {
        $teamInfoTransferList = $this->fussballDeClient->nextTeamGames(
            $this->getFussballDeRequest($id)
        );

        return new JsonResponse(['data' => $teamInfoTransferList]);
    }

    private function getFussballDeRequest(string $id): FussballDeRequest
    {
        $id = str_replace('#!', '', $id);

        $fussballDeRequest = new FussballDeRequest();
        $fussballDeRequest->id = $id;

        return $fussballDeRequest;
    }
}
