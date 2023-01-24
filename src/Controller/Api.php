<?php declare(strict_types=1);

namespace App\Controller;

use App\Component\Dto\FussballDeRequest;
use App\Component\FussballDe\FussballDeClientInterface;
use App\Component\User\UserFacadeInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api")
 */
class Api
{
    public const HEADER_AUTH_NAME = 'x-auth-token';

    public function __construct(
        private readonly FussballDeClientInterface $fussballDeClient,
        private readonly UserFacadeInterface       $userFacade,
    )
    {
    }

    /**
     * @Route("/club/{id}", name="api_club")
     */
    public function club(string $id, Request $request): JsonResponse
    {
        $this->checkAuthToken($request);

        $clubInfoTransferListForApi = $this->getInfoTransferListForApi($id);

        return new JsonResponse(['data' => $clubInfoTransferListForApi]);
    }

    /**
     * @Route("/club/info/{id}", name="api_club_info")
     */
    public function clubInfo(string $id, Request $request): JsonResponse
    {
        $this->checkAuthToken($request);

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
     * @Route("/club/prev_games/{id}", name="api_club_prev_games")
     */
    public function clubPrevGames(string $id, Request $request): JsonResponse
    {
        $this->checkAuthToken($request);

        $clubInfoTransferList = $this->fussballDeClient->prevClubGames(
            $this->getFussballDeRequest($id)
        );

        return new JsonResponse(['data' => $clubInfoTransferList]);
    }

    /**
     * @Route("/club/next_games/{id}", name="api_club_next_games")
     */
    public function clubNextGames(string $id, Request $request): JsonResponse
    {
        $this->checkAuthToken($request);

        $clubInfoTransferList = $this->fussballDeClient->nextClubGames(
            $this->getFussballDeRequest($id)
        );

        return new JsonResponse(['data' => $clubInfoTransferList]);
    }

    /**
     * @Route("/team/{id}", name="api_team")
     */
    public function team(string $id, Request $request): JsonResponse
    {
        $this->checkAuthToken($request);

        $fussballDeRequest = $this->getFussballDeRequest($id);

        $prevTeamGames = $this->fussballDeClient->prevTeamGames($fussballDeRequest);
        $nextTeamGames = $this->fussballDeClient->nextTeamGames($fussballDeRequest);
        $teamTable = $this->fussballDeClient->teamTable($fussballDeRequest);

        return new JsonResponse([
            'data' => [
                'prevGames' => $prevTeamGames,
                'nextGames' => $nextTeamGames,
                'table' => $teamTable,
            ],
        ]);
    }

    /**
     * @Route("/team/prev_games/{id}", name="api_team_prev_games")
     */
    public function teamPrevGames(string $id, Request $request): JsonResponse
    {
        $this->checkAuthToken($request);

        $teamInfoTransferList = $this->fussballDeClient->prevTeamGames(
            $this->getFussballDeRequest($id)
        );

        return new JsonResponse(['data' => $teamInfoTransferList]);
    }

    /**
     * @Route("/team/next_games/{id}", name="api_team_next_games")
     */
    public function teamNextGames(string $id, Request $request): JsonResponse
    {
        $this->checkAuthToken($request);

        $teamInfoTransferList = $this->fussballDeClient->nextTeamGames(
            $this->getFussballDeRequest($id)
        );

        return new JsonResponse(['data' => $teamInfoTransferList]);
    }

    /**
     * @Route("/team/table/{id}", name="api_team_table")
     */
    public function teamTable(string $id, Request $request): JsonResponse
    {
        $this->checkAuthToken($request);

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

    private function checkAuthToken(Request $request): void
    {
        $headerAuthToken = $request->headers->get(self::HEADER_AUTH_NAME);

        if (empty($headerAuthToken)) {
            throw new AccessDeniedHttpException(
                sprintf('Token in header: "%s" not found', self::HEADER_AUTH_NAME)
            );
        }

        if ($this->userFacade->issetToken($headerAuthToken) === false) {
            throw new UnauthorizedHttpException(
                $headerAuthToken,
                sprintf('Token "%s" not found', $headerAuthToken)
            );
        }
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
                'nextGames' => '/api/team/next_games/' . $clubInfoTransfer->id,
                'prevGames' => '/api/team/prev_games/' . $clubInfoTransfer->id,
                'table' => '/api/team/table/' . $clubInfoTransfer->id,
                'allInfo' => '/api/team/' . $clubInfoTransfer->id,
            ];

            $clubInfoTransferListForApi[] = $clubInfoTransferForApi;
        }
        
        return $clubInfoTransferListForApi;
    }
}
