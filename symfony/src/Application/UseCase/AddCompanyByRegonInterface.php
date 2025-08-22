<?php

declare(strict_types=1);

namespace App\Application\UseCase;

use App\Domain\Exception\AuthenticationFailedException;
use App\Domain\Exception\DuplicateException;
use UnexpectedValueException;

interface AddCompanyByRegonInterface
{
    /**
     * @throws AuthenticationFailedException
     * @throws UnexpectedValueException
     * @throws DuplicateException
     */
    public function execute(string $regon): void;
}
