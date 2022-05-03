<?php declare(strict_types=1);

namespace App\Tests\Api\Component\FussballDe\Font;

use App\Component\FussballDe\Font\Decode;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class DecodeTest extends KernelTestCase
{
    public function testDecode()
    {
        $decode = self::getContainer()->get(Decode::class);

        $info = $decode->decodeFont('odtkws4a');


        self::assertSame('3', $info['xe670']);
        self::assertSame('8', $info['xe694']);
        self::assertSame('1', $info['xe686']);
        self::assertSame('4', $info['xe6b3']);
    }
}
