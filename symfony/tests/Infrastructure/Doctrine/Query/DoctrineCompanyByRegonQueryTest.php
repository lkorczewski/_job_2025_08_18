<?php

namespace App\Tests\Infrastructure\Doctrine\Query;

use App\Domain\Company as DomainCompany;
use App\Infrastructure\Doctrine\Entity\Company;
use App\Infrastructure\Doctrine\Query\DoctrineCompanyByRegonQuery;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DoctrineCompanyByRegonQueryTest extends KernelTestCase
{
    private Connection $connection;

    private DoctrineCompanyByRegonQuery $query;

    private EntityManagerInterface $entityManager;

    public function setUp(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        $this->entityManager = $container->get(EntityManagerInterface::class);
        $this->connection = $container->get(Connection::class);
        $this->query = $container->get(DoctrineCompanyByRegonQuery::class);

        $this->connection->executeQuery('TRUNCATE TABLE companies RESTART IDENTITY;');
    }

    public function testQueryWhenTableEmpty(): void
    {
        self::assertSame([], $this->query->execute('000331501'));
    }

    public function testQueryWhenFound(): void
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
            ],
            $this->query->execute('000331501'));
    }

    public function testQueryWhenNotFound(): void
    {
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

        self::assertSame([],$this->query->execute('000331501'));
    }
}
