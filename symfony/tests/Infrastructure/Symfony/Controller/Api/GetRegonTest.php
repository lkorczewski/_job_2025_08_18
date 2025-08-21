<?php

namespace App\Tests\Infrastructure\Symfony\Controller\Api;

use App\Application\UseCase\ListCompaniesInterface;
use App\Infrastructure\Symfony\Controller\Api\GetRegon;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

class GetRegonTest extends TestCase
{
    private ListCompaniesInterface & MockObject $listCompanies;
    private GetRegon $controller;

    protected function setUp(): void
    {
        $this->listCompanies = $this->createMock(ListCompaniesInterface::class);
        $this->controller = new GetRegon($this->listCompanies);
    }

    public function testGetRegon(): void
    {
        $this->listCompanies->expects($this->once())->method('execute')->willReturn([
            1 => [
                'name' => 'Company 1',
                'regon' => 'Regon 1',
            ],
            2 => [
                'name' => 'Company 2',
                'regon' => 'Regon 2',
            ]
        ]);
        self::assertEquals(
            new JsonResponse([
                1 => [
                    'name' => 'Company 1',
                    'regon' => 'Regon 1',
                ],
                2 => [
                    'name' => 'Company 2',
                    'regon' => 'Regon 2',
                ]
            ]),
            $this->controller->__invoke()
        );
    }
}
