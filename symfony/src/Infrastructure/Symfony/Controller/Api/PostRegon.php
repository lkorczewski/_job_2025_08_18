<?php

namespace App\Infrastructure\Symfony\Controller\Api;

use App\Application\Query\CompanyByRegonQuery;
use App\Application\UseCase\AddCompanyByRegonInterface;
use App\Domain\Exception\AuthenticationFailedException;
use App\Domain\Exception\DuplicateException;
use App\Domain\Exception\NotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use UnexpectedValueException;

final readonly class PostRegon
{
    public function __construct(
        private AddCompanyByRegonInterface $addCompanyByRegon,
        private CompanyByRegonQuery $companyByRegonQuery
    ) {
    }

    #[Route('/api/regon', name: 'api_post_regon', methods: ['POST'])]
    public function __invoke(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['regon'])) {
            return new JsonResponse(['error' => 'Missing "regon" field'], 400);
        }

        if (!is_string($data['regon'])) {
            return new JsonResponse(['error' => 'Invalid "regon" field'], 400);
        }

        try {
            $this->addCompanyByRegon->execute($data['regon']);
        } catch (UnexpectedValueException $exception) {
            return new JsonResponse(['error' => $exception->getMessage()], 400);
        } catch (AuthenticationFailedException $exception) {
            return new JsonResponse(['error' => 'Unable to communicate with dependent service'], 500);
        } catch (NotFoundException $exception) {
            return new JsonResponse(['error' => $exception->getMessage()], 404);
        } catch (DuplicateException) {
            return new JsonResponse(['error' => 'Duplicate REGON'], 409);
        }

        return new JsonResponse($this->companyByRegonQuery->execute($data['regon']));
    }
}
