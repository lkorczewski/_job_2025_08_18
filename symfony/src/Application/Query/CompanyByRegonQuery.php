<?php

namespace App\Application\Query;

interface CompanyByRegonQuery
{
    public function execute(string $regon): array;
}
