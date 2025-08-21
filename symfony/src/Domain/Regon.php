<?php

namespace App\Domain;

use UnexpectedValueException;

final readonly class Regon
{
    private const array WEIGHTS = [8, 9, 2, 3, 4, 5, 6, 7];

    private string $value;

    public function __construct(string $regon)
    {
        $this->validate($regon);

        $this->value = $regon;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    private function validate(string $regon): void
    {
        if (strlen($regon) !== 9) {
            throw new UnexpectedValueException('Invalid regon: 9 characters required');
        }

        if (!ctype_digit($regon)) {
            throw new UnexpectedValueException('Invalid regon: contains non-numeric characters');
        }

        $regonArray = str_split($regon);

        $sum = array_sum(array_map(fn($x, $y) => $x * $y, $regonArray, self::WEIGHTS));

        if ((int)$regonArray[8] !== $sum % 11 % 10) {
            throw new UnexpectedValueException('Invalid regon: incorrect control sum');
        }
    }
}
