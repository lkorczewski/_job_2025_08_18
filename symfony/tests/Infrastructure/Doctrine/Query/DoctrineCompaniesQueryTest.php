<?php

namespace App\Tests\Infrastructure\Doctrine\Query;

use App\Domain\Company as DomainCompany;
use App\Infrastructure\Doctrine\Entity\Company;
use App\Infrastructure\Doctrine\Query\DoctrineCompaniesQuery;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DoctrineCompaniesQueryTest extends KernelTestCase
{
    private Connection $connection;

    private DoctrineCompaniesQuery $query;

    private EntityManagerInterface $entityManager;

    public function setUp(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        $this->entityManager = $container->get(EntityManagerInterface::class);
        $this->connection = $container->get(Connection::class);
        $this->query = $container->get(DoctrineCompaniesQuery::class);

        $this->connection->executeQuery('TRUNCATE TABLE companies RESTART IDENTITY;');
    }

    public function testQueryWhenEmpty(): void
    {
        self::assertSame([], $this->query->execute());
    }

    public function testQueryWithData(): void
    {
        $this->entityManager->persist(Company::fromDomain(new DomainCompany(
            name: 'GŁÓWNY URZĄD STATYSTYCZNY',
            regon: '000331501',
            province: 'MAZOWIECKIE',
            district: 'm. st. Warszawa',
            community: 'Śródmieście',
            zipCode: '00-925',
            city: 'Warszawa',
            street: 'ul. Test-Krucza',
            propertyNumber: '208'
        )));
        $this->entityManager->persist(Company::fromDomain(new DomainCompany(
            name: '"JW LOGISTICS" SPÓŁKA Z OGRANICZONĄ ODPOWIEDZIALNOŚCIĄ',
            regon: '015460900',
            province: 'MAZOWIECKIE',
            district: 'warszawski zachodni',
            community: 'Ożarów Mazowiecki',
            zipCode: '05-860',
            city: 'Święcice',
            street: 'ul. Test-Krucza',
            propertyNumber: '1'
        )));
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
                2 => [
                    'name' => '"JW LOGISTICS" SPÓŁKA Z OGRANICZONĄ ODPOWIEDZIALNOŚCIĄ',
                    'regon' => '015460900',
                    'province' => 'MAZOWIECKIE',
                    'district' => 'warszawski zachodni',
                    'community' => 'Ożarów Mazowiecki',
                    'zip_code' => '05-860',
                    'city' => 'Święcice',
                    'street' => 'ul. Test-Krucza',
                    'property_number' => '1',
                ],
            ],
            $this->query->execute()
        );
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }
}
