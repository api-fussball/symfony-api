<?php declare(strict_types=1);

namespace App\Tests\Api\Component\FussballDe\Font;

use App\Component\FussballDe\Font\Decode;
use App\Component\FussballDe\Font\DecodeProxyInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

final class DecodeTest extends KernelTestCase
{
    private string $cacheDirname;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cacheDirname = self::getContainer()->get(ParameterBagInterface::class)->get('kernel.cache_dir') . '/fonts';
    }

    protected function tearDown(): void
    {
        array_map('unlink', glob($this->cacheDirname . '/*.*'));
        rmdir($this->cacheDirname);

        parent::tearDown();
    }

    public function testDecode()
    {
        $decode = self::getContainer()->get(DecodeProxyInterface::class);

        $info = $decode->decodeFont('odtkws4a');

        self::assertSame('3', $info['xe670']);
        self::assertSame('8', $info['xe694']);
        self::assertSame('1', $info['xe686']);
        self::assertSame('4', $info['xe6b3']);

        self::assertStringEqualsFile($this->cacheDirname . '/odtkws4a.json', json_encode($info), );
    }
}
