<?php

namespace App\Domain;

use App\Domain\Exception\DuplicateException;

interface CompanyRepository
{
    /** @throws DuplicateException */
    public function save(Company $company): void;
}
