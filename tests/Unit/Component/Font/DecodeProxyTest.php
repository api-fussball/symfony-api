<?php declare(strict_types=1);

namespace App\Tests\Unit\Component\Font;

use App\Component\FussballDe\Font\DecodeInterface;
use App\Component\FussballDe\Font\DecodeProxy;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

class DecodeProxyTest extends TestCase
{
    protected function tearDown(): void
    {
        array_map('unlink', glob(__DIR__ . '/tmp/fonts/*.*'));
        if (is_dir(__DIR__ . '/tmp/fonts')) {
            rmdir(__DIR__ . '/tmp/fonts');
        }

        parent::tearDown();
    }

    /**
     * Test for code coverage :-P
     *
     * @return void
     */
    public function testWhenKernelCacheDirIsIncorrectFound(): void
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('CacheDir not found');

        $decode = $this->createStub(DecodeInterface::class);
        $parameterBag = new ParameterBag(['kernel.cache_dir' => false]);

        new DecodeProxy($decode, $parameterBag);
    }

    /**
     * Test for code coverage :-P
     *
     * @return void
     */
    public function testWhenKCacheFileIsIncorrect(): void
    {
        $decode = $this->createMock(DecodeInterface::class);
        $decode->expects($this->never())
            ->method('decodeFont');

        $dir = __DIR__ . '/tmp/fonts';
        mkdir($dir);
        file_put_contents($dir . '/abc.json', 'unit-fail');

        $parameterBag = new ParameterBag(['kernel.cache_dir' => __DIR__ . '/tmp']);

        $decodeProxy = new DecodeProxy($decode, $parameterBag);
        self::assertSame(
            [],
            $decodeProxy->decodeFont('abc')
        );
    }

    public function testCreateFontsDir(): void
    {
        $decode = $this->createMock(DecodeInterface::class);
        $decode->expects($this->once())
            ->method('decodeFont')
            ->willReturn([1,2,3,4]);

        $parameterBag = new ParameterBag(['kernel.cache_dir' => __DIR__ . '/tmp']);

        $decodeProxy = new DecodeProxy($decode, $parameterBag);
        self::assertSame(
            [1,2,3,4],
            $decodeProxy->decodeFont('unit-font-file')
        );

        self::assertDirectoryExists(__DIR__ . '/tmp/fonts');
        self::assertFileEquals(__DIR__ . '/tmp/fonts/unit-font-file.json', json_encode([1,2,3,4]));
    }
}
