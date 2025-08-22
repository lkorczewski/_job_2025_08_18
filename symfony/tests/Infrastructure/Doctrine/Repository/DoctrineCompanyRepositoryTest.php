<?php

namespace App\Tests\Infrastructure\Doctrine\Repository;

use App\Domain\Company as DomainCompany;
use App\Domain\Exception\DuplicateException;
use App\Infrastructure\Doctrine\Entity\Company;
use App\Infrastructure\Doctrine\Repository\DoctrineCompanyRepository;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DoctrineCompanyRepositoryTest extends KernelTestCase
{
    private Connection $connection;

    private DoctrineCompanyRepository $repository;

    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        $this->entityManager = $container->get(EntityManagerInterface::class);
        $this->connection = $container->get(Connection::class);
        $this->repository = $container->get(DoctrineCompanyRepository::class);

        $this->connection->executeQuery('TRUNCATE TABLE companies RESTART IDENTITY;');
    }

    public function testSaving(): void
    {
        $this->repository->save(new DomainCompany(
            name: 'GŁÓWNY URZĄD STATYSTYCZNY',
            regon: '000331501',
            province: 'MAZOWIECKIE',
            district: 'm. st. Warszawa',
            community: 'Śródmieście',
            zipCode: '00-925',
            city: 'Warszawa',
            street: 'ul. Test-Krucza',
            propertyNumber: '208'
        ));
        $this->entityManager->flush();

        self::assertSame(
            [
                1 => [
                    'name' => 'GŁÓWNY URZĄD STATYSTYCZNY',
                    'regon' => '000331501',
                    'province' => 'MAZOWIECKIE',
                    'district' => 'm. st. Warszawa',
                    'community' => 'Śródmieście',
                    'zip_code' => '00-925',
                    'city' => 'Warszawa',
                    'street' => 'ul. Test-Krucza',
                    'property_number' => '208',
                ],
            ],
            $this->connection->fetchAllAssociativeIndexed('SELECT * FROM companies;')
        );
    }

    public function testSavingWhenDuplicate(): void
    {
        self::expectException(DuplicateException::class);

        $this->repository->save(new DomainCompany(
            name: 'GŁÓWNY URZĄD STATYSTYCZNY',
            regon: '000331501',
            province: 'MAZOWIECKIE',
            district: 'm. st. Warszawa',
            community: 'Śródmieście',
            zipCode: '00-925',
            city: 'Warszawa',
            street: 'ul. Test-Krucza',
            propertyNumber: '208'
        ));
        $this->repository->save(new DomainCompany(
            name: 'GŁÓWNY URZĄD STATYSTYCZNY',
            regon: '000331501',
            province: 'MAZOWIECKIE',
            district: 'm. st. Warszawa',
            community: 'Śródmieście',
            zipCode: '00-925',
            city: 'Warszawa',
            street: 'ul. Test-Krucza',
            propertyNumber: '208'
        ));
        $this->entityManager->flush();
    }
}
