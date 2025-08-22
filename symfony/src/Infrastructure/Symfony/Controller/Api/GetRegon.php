<?php

declare(strict_types=1);

namespace App\Infrastructure\Symfony\Controller\Api;

use App\Application\UseCase\ListCompaniesInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final readonly class GetRegon
{
    public function __construct(
        private ListCompaniesInterface $listCompanies
    ) {
    }

    #[Route('/api/regon', name: 'api_get_regon', methods: ['GET'])]
    public function __invoke(): Response
    {
        $result = $this->listCompanies->execute();

        return new JsonResponse($result);
    }
}
