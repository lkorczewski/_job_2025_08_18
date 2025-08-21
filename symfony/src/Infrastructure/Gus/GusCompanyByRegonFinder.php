<?php

namespace App\Infrastructure\Gus;

use App\Domain\Company;
use App\Domain\CompanyByRegonFinder;
use App\Domain\Exception\AuthenticationFailedException;
use App\Domain\Exception\NotFoundException as DomainNotFoundException;
use App\Domain\Regon;
use GusApi\Exception\InvalidUserKeyException;
use GusApi\Exception\NotFoundException;
use GusApi\GusApi;
use GusApi\SearchReport;

final readonly class GusCompanyByRegonFinder implements CompanyByRegonFinder
{
    public function __construct(private GusApi $gusApi)
    {
    }

    public function execute(Regon $regon): Company
    {
        $this->ensureLoggedIn();

        $report = $this->findCompanyByRegon($regon);

        return new Company(
            name: $report->getName(),
            regon: $report->getRegon(),
            province: $report->getProvince(),
            district: $report->getDistrict(),
            community: $report->getCommunity(),
            zipCode: $report->getZipCode(),
            city: $report->getCity(),
            street: $report->getStreet(),
            propertyNumber: $report->getPropertyNumber(),
        );
    }

    public function ensureLoggedIn(): void
    {
        if (!$this->gusApi->isLogged()) {
            try {
                $this->gusApi->login();
            } catch (InvalidUserKeyException $exception) {
                throw new AuthenticationFailedException('Invalid user key', previous: $exception);
            }
        }
    }

    public function findCompanyByRegon(Regon $regon): SearchReport
    {
        try {
            $reports = $this->gusApi->getByRegon($regon);
        } catch (NotFoundException $exception) {
            throw new DomainNotFoundException('Regon not registered at GUS', previous: $exception);
        }

        return $reports[0];
    }
}
