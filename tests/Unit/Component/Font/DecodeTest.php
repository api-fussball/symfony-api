<?php declare(strict_types=1);

namespace App\Tests\Unit\Component\Font;

use App\Component\FussballDe\Font\Decode;
use Hoa\Iterator\Directory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class DecodeTest extends TestCase
{
    protected function tearDown(): void
    {
        array_map('unlink', glob(__DIR__ . '/tmp/fonts/*.*'));
        rmdir(__DIR__ . '/tmp/fonts');

        parent::tearDown();
    }

    public function testUniCodeInfo(): void
    {
        $mockHttpClient = $this->getHttpClientMock();
        $parameterBag = $this->getParameterBagMock();

        $decode = new Decode($mockHttpClient, $parameterBag);

        $unicode = $decode->decodeFont('83vk3pzh');

        self::assertSame('3', $unicode['xe650']);
        self::assertSame('3', $unicode['xe651']);
        self::assertSame('7', $unicode['xe652']);
        self::assertSame('5', $unicode['xe654']);
        self::assertSame('0', $unicode['xe65a']);
        self::assertSame('-', $unicode['xe665']);
    }

    public function testCreateFolderAndClearTmpFiles()
    {
        $mockHttpClient = $this->getHttpClientMock();
        $parameterBag = $this->getParameterBagMock();

        $decode = new Decode($mockHttpClient, $parameterBag);

        $decode->decodeFont('unit');

        self::assertDirectoryExists(__DIR__ . '/tmp/fonts');

        self::assertFileDoesNotExist(__DIR__ . '/tmp/fonts/unit.woff');
        self::assertFileDoesNotExist(__DIR__ . '/tmp/fonts/unit.ttx');

        $di = new \DirectoryIterator(__DIR__ . '/tmp/fonts');
        foreach ($di as $file) {
            if ($file->isFile()) {
                $this->fail('File exist:');
            }
        }
    }


    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|\Symfony\Contracts\HttpClient\HttpClientInterface
     */
    private function getHttpClientMock(): HttpClientInterface|MockObject
    {
        $mockResponse = $this->createMock(ResponseInterface::class);
        $mockResponse->method('getContent')
            ->willReturn(
                file_get_contents(__DIR__ . '/../../../_data/font/main.woff')
            );

        $mockHttpClient = $this->createMock(HttpClientInterface::class);
        $mockHttpClient->method('request')
            ->willReturn($mockResponse);
        return $mockHttpClient;
    }

    /**
     * @return \Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface
     */
    private function getParameterBagMock(): ParameterBagInterface
    {
        return new ParameterBag([
            'kernel.cache_dir' => __DIR__ . '/tmp',
        ]);
    }
}
