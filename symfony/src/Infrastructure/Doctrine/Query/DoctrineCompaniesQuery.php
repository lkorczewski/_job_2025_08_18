<?php

namespace App\Infrastructure\Doctrine\Query;

use App\Application\Query\CompaniesQuery;
use Doctrine\DBAL\Connection;

final readonly class DoctrineCompaniesQuery implements CompaniesQuery
{
    public function __construct(private Connection $connection)
    {
    }

    public function execute(): array
    {
        return $this->connection->fetchAllAssociativeIndexed(
            'SELECT * FROM companies'
        ) ?: [];
    }
}
