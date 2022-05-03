<?php declare(strict_types=1);

namespace App\Tests\Unit\Component\Font;

use App\Component\FussballDe\Font\Decode;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class DecodeTest extends TestCase
{
    public function test()
    {
        $mock = $this->createMock(HttpClientInterface::class);
        $mock->method('request')->willReturn('test');

        $decode = new Decode($mock);

        $unicoe = $decode->decodeFont('83vk3pzh');

        self::assertSame('3', $unicoe['xe650']);
        self::assertSame('3', $unicoe['xe651']);
        self::assertSame('7', $unicoe['xe652']);
        self::assertSame('5', $unicoe['xe654']);
        self::assertSame('0', $unicoe['xe65a']);
        self::assertSame('-', $unicoe['xe665']);
    }
}
