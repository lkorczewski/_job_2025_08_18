<?php

declare(strict_types=1);

namespace App\Application\UseCase;

interface ListCompaniesInterface
{
    public function execute(): array;
}
