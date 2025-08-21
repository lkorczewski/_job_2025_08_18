<?php

namespace App\Tests\Infrastructure\Symfony\Controller\Api;

use App\Application\Query\CompanyByRegonQuery;
use App\Application\UseCase\AddCompanyByRegonInterface;
use App\Domain\Exception\AuthenticationFailedException;
use App\Domain\Exception\DuplicateException;
use App\Domain\Exception\NotFoundException;
use App\Infrastructure\Symfony\Controller\Api\PostRegon;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use UnexpectedValueException;

class PostRegonTest extends TestCase
{
    private AddCompanyByRegonInterface & MockObject $addCompanyByRegon;

    private CompanyByRegonQuery & MockObject $companyByRegonQuery;

    private PostRegon $controller;

    protected function setUp(): void
    {
        $this->addCompanyByRegon = $this->createMock(AddCompanyByRegonInterface::class);
        $this->companyByRegonQuery = $this->createMock(CompanyByRegonQuery::class);
        $this->controller = new PostRegon(
            $this->addCompanyByRegon,
            $this->companyByRegonQuery
        );
    }

    public function testAddCompanyByRegon(): void
    {
        $this->addCompanyByRegon->expects($this->once())->method('execute')->with('123');

        $this->companyByRegonQuery
            ->method('execute')
            ->willReturn([13 => ['name' => 'Name 1', 'regon' => '123']]);

        self::assertEquals(
            new JsonResponse([13 => ['name' => 'Name 1', 'regon' => '123']], 200),
            $this->controller->__invoke(new Request(content: '{"regon": "123"}'))
        );
    }

    public function testAddCompanyByRegonWhenNoRegon(): void
    {
        $this->addCompanyByRegon->expects($this->never())->method('execute');

        self::assertEquals(
            new JsonResponse(['error' => 'Missing "regon" field'], 400),
            $this->controller->__invoke(new Request(content: '{}'))
        );
    }

    public function testAddCompanyByRegonNotString(): void
    {
        $this->addCompanyByRegon->expects($this->never())->method('execute');

        self::assertEquals(
            new JsonResponse(['error' => 'Invalid "regon" field'], 400),
            $this->controller->__invoke(new Request(content: '{"regon": 123}'))
        );
    }

    public function testAddCompanyByRegonWhenInvalidRegon(): void
    {
        $this->addCompanyByRegon->expects($this->once())->method('execute')->with('123')
            ->willThrowException(new UnexpectedValueException('Invalid REGON'));

        $this->companyByRegonQuery
            ->method('execute')
            ->willReturn([13 => ['name' => 'Name 1', 'regon' => '123']]);

        self::assertEquals(
            new JsonResponse(['error' => 'Invalid REGON'], 400),
            $this->controller->__invoke(new Request(content: '{"regon": "123"}'))
        );
    }

    public function testAddCompanyByRegonWhenAuthenticationFailed(): void
    {
        $this->addCompanyByRegon->expects($this->once())->method('execute')->with('123')
            ->willThrowException(new AuthenticationFailedException('Invalid user key'));

        $this->companyByRegonQuery
            ->method('execute')
            ->willReturn([13 => ['name' => 'Name 1', 'regon' => '123']]);

        self::assertEquals(
            new JsonResponse(['error' => 'Unable to communicate with dependent service'], 500),
            $this->controller->__invoke(new Request(content: '{"regon": "123"}'))
        );
    }

    public function testAddCompanyByRegonWhenCompanyNotFound(): void
    {
        $this->addCompanyByRegon->expects($this->once())->method('execute')->with('123')
            ->willThrowException(new NotFoundException('Regon not registered at GUS'));

        $this->companyByRegonQuery
            ->method('execute')
            ->willReturn([13 => ['name' => 'Name 1', 'regon' => '123']]);

        self::assertEquals(
            new JsonResponse(['error' => 'Regon not registered at GUS'], 404),
            $this->controller->__invoke(new Request(content: '{"regon": "123"}'))
        );
    }

    public function testAddCompanyByRegonWhenDuplicateRegon(): void
    {
        $this->addCompanyByRegon->expects($this->once())->method('execute')->with('123')
            ->willThrowException(new DuplicateException('Company already exists.'));

        $this->companyByRegonQuery
            ->method('execute')
            ->willReturn([13 => ['name' => 'Name 1', 'regon' => '123']]);

        self::assertEquals(
            new JsonResponse(['error' => 'Duplicate REGON'], 409),
            $this->controller->__invoke(new Request(content: '{"regon": "123"}'))
        );
    }
}
