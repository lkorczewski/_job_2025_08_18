<?php

declare(strict_types=1);

namespace App\Application\Query;

interface CompaniesQuery
{
    public function execute(): array;
}
