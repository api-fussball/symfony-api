<?php declare(strict_types=1);

namespace App\Controller;

use App\Component\User\UserFacadeInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/auth")
 */
class Auth
{
    public function __construct(
        private readonly UserFacadeInterface $userFacade,
    )
    {
    }

    /**
     * @Route("/register", name="register", methods={"POST"})
     */
    public function register(Request $request): JsonResponse
    {
        $isSuccess = true;
        $status = 200;
        $data = [];

        $content = $this->getContent($request);
        $info = (array)json_decode($content, true);

        try {
            if(!isset($info['email'])) {
                throw new \RuntimeException('Error! Field user not found');
            }
            $email = filter_var($info['email'], FILTER_SANITIZE_EMAIL);

            $userTransfer = $this->userFacade->save($email);

            $data['token'] = $userTransfer->token;
            $data['email'] = $userTransfer->email;
            $message = 'Please copy the token. After leaving the page, copying again is not possible.';

        } catch (\Exception $e) {
            $message = $e->getMessage();
            $isSuccess = false;
            $status = 400;
        }

        return new JsonResponse(
            [
                'success' => $isSuccess,
                'message' => $message,
                'data' => $data,
            ],
            $status
        );
    }

    private function getContent(Request $request): string
    {
        return $request->getContent();
    }
}
