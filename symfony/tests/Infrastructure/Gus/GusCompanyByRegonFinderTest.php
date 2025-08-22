<?php

namespace App\Tests\Infrastructure\Gus;

use App\Domain\Company;
use App\Domain\Exception\AuthenticationFailedException;
use App\Domain\Exception\NotFoundException as DomainNotFoundExceptionAlias;
use App\Domain\Regon;
use App\Infrastructure\Gus\GusCompanyByRegonFinder;
use GusApi\Exception\InvalidUserKeyException;
use GusApi\Exception\NotFoundException;
use GusApi\GusApi;
use GusApi\SearchReport;
use GusApi\Type\Response\SearchResponseCompanyData;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class GusCompanyByRegonFinderTest extends TestCase
{
    private GusApi & MockObject $gusApi;

    private GusCompanyByRegonFinder $gusCompanyByRegonFinder;

    protected function setUp(): void
    {
        $this->gusApi = $this->createMock(GusApi::class);
        $this->gusCompanyByRegonFinder = new GusCompanyByRegonFinder($this->gusApi);
    }

    public function testGettingCompanyByRegonWhenNotLogged(): void
    {
        $this->gusApi->method('isLogged')->willReturn(false);
        $this->gusApi->expects(self::once())->method('login');

        $searchResponseCompanyData = new SearchResponseCompanyData();
        $searchResponseCompanyData->Nazwa = 'GŁÓWNY URZĄD STATYSTYCZNY';
        $searchResponseCompanyData->Regon = '000331501';
        $searchResponseCompanyData->Wojewodztwo = 'MAZOWIECKIE';
        $searchResponseCompanyData->Powiat = 'm. st. Warszawa';
        $searchResponseCompanyData->Gmina = 'Śródmieście';
        $searchResponseCompanyData->KodPocztowy = '00-925';
        $searchResponseCompanyData->Miejscowosc = 'Warszawa';
        $searchResponseCompanyData->Ulica = 'ul. Test-Krucza';
        $searchResponseCompanyData->NrNieruchomosci = '208';

        $this->gusApi->method('getByRegon')->with('000331501')
            ->willReturn([new SearchReport(
                $searchResponseCompanyData
            )]);

        self::assertEquals(
            new Company(
                name: 'GŁÓWNY URZĄD STATYSTYCZNY',
                regon: '000331501',
                province: 'MAZOWIECKIE',
                district: 'm. st. Warszawa',
                community: 'Śródmieście',
                zipCode: '00-925',
                city: 'Warszawa',
                street: 'ul. Test-Krucza',
                propertyNumber: '208'
            ),
            $this->gusCompanyByRegonFinder->execute(new Regon('000331501'))
        );
    }

    public function testGettingCompanyByRegonWhenLogged(): void
    {
        $this->gusApi->method('isLogged')->willReturn(true);
        $this->gusApi->expects(self::never())->method('login');

        $searchResponseCompanyData = new SearchResponseCompanyData();
        $searchResponseCompanyData->Nazwa = 'GŁÓWNY URZĄD STATYSTYCZNY';
        $searchResponseCompanyData->Regon = '000331501';
        $searchResponseCompanyData->Wojewodztwo = 'MAZOWIECKIE';
        $searchResponseCompanyData->Powiat = 'm. st. Warszawa';
        $searchResponseCompanyData->Gmina = 'Śródmieście';
        $searchResponseCompanyData->KodPocztowy = '00-925';
        $searchResponseCompanyData->Miejscowosc = 'Warszawa';
        $searchResponseCompanyData->Ulica = 'ul. Test-Krucza';
        $searchResponseCompanyData->NrNieruchomosci = '208';

        $this->gusApi->method('getByRegon')->with('000331501')
            ->willReturn([new SearchReport(
                $searchResponseCompanyData
            )]);

        self::assertEquals(
            new Company(
                name: 'GŁÓWNY URZĄD STATYSTYCZNY',
                regon: '000331501',
                province: 'MAZOWIECKIE',
                district: 'm. st. Warszawa',
                community: 'Śródmieście',
                zipCode: '00-925',
                city: 'Warszawa',
                street: 'ul. Test-Krucza',
                propertyNumber: '208'
            ),
            $this->gusCompanyByRegonFinder->execute(new Regon('000331501'))
        );
    }

    public function testGettingCompanyByRegonWhenInvalidUserKey(): void
    {
        self::expectException(AuthenticationFailedException::class);

        $this->gusApi->method('isLogged')->willReturn(false);
        $this->gusApi->method('login')->willThrowException(new InvalidUserKeyException());

        $this->gusCompanyByRegonFinder->execute(new Regon('000331501'));
    }

    public function testGettingCompanyByRegonWhenNotFound(): void
    {
        self::expectException(DomainNotFoundExceptionAlias::class);

        $this->gusApi->method('isLogged')->willReturn(true);

        $this->gusApi->method('getByRegon')->with('000331501')
            ->willThrowException(new NotFoundException());

        $this->gusCompanyByRegonFinder->execute(new Regon('000331501'));
    }
}
