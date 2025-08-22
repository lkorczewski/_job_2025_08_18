<?php

declare(strict_types=1);

namespace App\Application\Query;

interface CompanyByRegonQuery
{
    public function execute(string $regon): array;
}
