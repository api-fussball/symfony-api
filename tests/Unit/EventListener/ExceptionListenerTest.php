<?php declare(strict_types=1);

namespace App\Tests\Unit\EventListener;

use App\EventListener\ExceptionListener;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

final class ExceptionListenerTest extends TestCase
{
    public function testSetStatusCodeWhenExceptionNotFound()
    {
        $exceptionListener = new ExceptionListener();

        $exceptionEvent = new ExceptionEvent(
            $this->createMock(HttpKernelInterface::class),
            new Request(),
            100,
            new \Exception(),
        );

        $exceptionListener->onKernelException($exceptionEvent);


        $response = $exceptionEvent->getResponse();

        self::assertSame(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
        self::assertSame('application/json', $response->headers->get('Content-Type'));
    }
}
