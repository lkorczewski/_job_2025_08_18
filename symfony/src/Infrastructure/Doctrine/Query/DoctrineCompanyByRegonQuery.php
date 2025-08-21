<?php

namespace App\Infrastructure\Doctrine\Query;

use App\Application\Query\CompanyByRegonQuery;
use Doctrine\DBAL\Connection;

final readonly class DoctrineCompanyByRegonQuery implements CompanyByRegonQuery
{
    public function __construct(private Connection $connection)
    {
    }

    public function execute(string $regon): array
    {
        return $this->connection->fetchAllAssociativeIndexed(
            'SELECT * FROM companies WHERE regon = :regon',
            ['regon' => $regon]
        ) ?: [];
    }
}
