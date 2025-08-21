<?php

namespace App\Application\UseCase;

use App\Domain\CompanyByRegonFinder;
use App\Domain\CompanyRepository;
use App\Domain\Exception\AuthenticationFailedException;
use App\Domain\Exception\DuplicateException;
use App\Domain\Regon;
use UnexpectedValueException;

final readonly class AddCompanyByRegon implements AddCompanyByRegonInterface
{
    public function __construct(
        private CompanyByRegonFinder $companyByRegon,
        private CompanyRepository    $companyRepository,
    ) {
    }

    /**
     * @throws AuthenticationFailedException
     * @throws UnexpectedValueException
     * @throws DuplicateException
     */
    public function execute(string $regon): void
    {
        $regon = new Regon($regon);
        $company = $this->companyByRegon->execute($regon);

        $this->companyRepository->save($company);
    }
}
