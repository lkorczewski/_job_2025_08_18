<?php

namespace App\Tests\Domain;

use App\Domain\Regon;
use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use UnexpectedValueException;
use PHPUnit\Framework\TestCase;

class RegonTest extends TestCase
{

    #[DataProvider('provideValidRegons')]
    public function testValidRegon(string $regon): void
    {
        $regonObject = new Regon($regon);

        self::assertSame($regon, (string)$regonObject);
    }

    public static function provideValidRegons(): Generator
    {
        yield 'GUS'              => ['regon' => '000331501'];
        yield 'control sum = 10' => ['regon' => '015460900'];
    }

    #[DataProvider('provideInvalidRegons')]
    public function testInvalidRegon(string $regon): void
    {
        self::expectException(UnexpectedValueException::class);

        new Regon($regon);
    }

    public static function provideInvalidRegons(): array
    {
        return [
            'empty'                 => [''],
            'too short'             => ['00331501'],
            'too long'              => ['0000331501'],
            'invalid character'     => ['0003A1501'],
            'incorrect control sum' => ['00331502'],
        ];
    }
}
