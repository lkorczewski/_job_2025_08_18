<?php

namespace App\Domain;

final readonly class Company
{
    public function __construct(
        public string $name,
        public string $regon,
        public string $province,
        public string $district,
        public string $community,
        public string $zipCode,
        public string $city,
        public string $street,
        public string $propertyNumber,
    ) {
    }
}
