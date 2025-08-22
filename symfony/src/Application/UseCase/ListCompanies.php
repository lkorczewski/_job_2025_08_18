<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Application\Query\CompaniesQuery;

final readonly class ListCompanies implements ListCompaniesInterface
{
    public function __construct(
        private CompaniesQuery $companiesQuery,
    ) {
    }

    public function execute(): array
    {
        return $this->companiesQuery->execute();
    }
}
