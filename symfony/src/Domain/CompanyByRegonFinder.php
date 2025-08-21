<?php

namespace App\Domain;

interface CompanyByRegonFinder
{
    public function execute(Regon $regon): Company;
}
