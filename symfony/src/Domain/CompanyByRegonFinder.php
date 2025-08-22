<?php

declare(strict_types=1);

namespace App\Domain;

interface CompanyByRegonFinder
{
    public function execute(Regon $regon): Company;
}
