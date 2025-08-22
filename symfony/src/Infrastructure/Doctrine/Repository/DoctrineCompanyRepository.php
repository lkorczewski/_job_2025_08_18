<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Repository;

use App\Domain\Company as DomainCompany;
use App\Domain\CompanyRepository;
use App\Domain\Exception\DuplicateException;
use App\Infrastructure\Doctrine\Entity\Company;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;

final readonly class DoctrineCompanyRepository implements CompanyRepository
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function save(DomainCompany $company): void
    {

        $this->entityManager->persist(Company::fromDomain($company));

        try {
            $this->entityManager->flush();
        } catch (UniqueConstraintViolationException $exception) {
            throw new DuplicateException('Company already exists.', previous: $exception);
        }
    }
}
