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
     * @Route("/club/info/{id}", name="api_club_info")
     */
    public function clubInfo(string $id): JsonResponse
    {
        $clubInfoTransferListForApi = $this->getInfoTransferListForApi($id);

        $fussballDeRequest = $this->getFussballDeRequest($id);

        $clubPrevGamesInfoTransferList = $this->fussballDeClient->prevClubGames($fussballDeRequest);
        $clubNextGamesInfoTransferList = $this->fussballDeClient->nextClubGames($fussballDeRequest);

        return new JsonResponse([
                'data' => [
                    'clubs' => $clubInfoTransferListForApi,
                    'prevGames' => $clubPrevGamesInfoTransferList,
                    'nextGames' => $clubNextGamesInfoTransferList,
                ],
            ]
        );
    }

    /**
     * @Route("/club/{id}", name="api_club")
     */
    public function club(string $id): JsonResponse
    {
        $clubInfoTransferListForApi = $this->getInfoTransferListForApi($id);

        return new JsonResponse(['data' => $clubInfoTransferListForApi]);
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


    /**
     * @Route("/team/table/{id}", name="api_team_table")
     */
    public function teamTable(string $id): JsonResponse
    {
        $teamInfoTransferList = $this->fussballDeClient->teamTable(
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

    /**
     * @param string $id
     *
     * @return array
     */
    private function getInfoTransferListForApi(string $id): array
    {
        $clubInfoTransferList = $this->fussballDeClient->teamsInfo(
            $this->getFussballDeRequest($id)
        );

        $clubInfoTransferListForApi = [];
        foreach ($clubInfoTransferList as $clubInfoTransfer) {
            $clubInfoTransferForApi = (array)$clubInfoTransfer;
            unset($clubInfoTransferForApi['id']);

            $clubInfoTransferForApi['urls'] = [
                'nextGames' => '/club/next_games/' . $clubInfoTransfer->id,
                'prevGames' => '/club/prev_games/' . $clubInfoTransfer->id,
                'table' => '/club/table/' . $clubInfoTransfer->id,
            ];

            $clubInfoTransferListForApi[] = $clubInfoTransferForApi;
        }
        return $clubInfoTransferListForApi;
    }
}
