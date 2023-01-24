<?php declare(strict_types=1);

namespace App\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ExceptionListener
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $request = $event->getRequest();

        $data = [
            'message' => $exception->getMessage(),
            'success' => false,
            'data' => [],
        ];

        if($request->server->get('APP_ENV') !== 'prod') {
            $data['traces'] = $exception->getTrace();
        }

        $response = new JsonResponse($data);

        if (!$exception instanceof HttpExceptionInterface) {
            $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $event->setResponse($response);
    }
}
