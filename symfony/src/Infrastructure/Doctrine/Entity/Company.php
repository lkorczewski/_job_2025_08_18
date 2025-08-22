<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Entity;

use App\Domain\Company as DomainCompany;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'companies')]
class Company
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    public string $id;

    #[ORM\Column(type: "string", length: 255)]
    public string $name;

    #[ORM\Column(type: "string", length: 9, unique: true)]
    public string $regon;

    #[ORM\Column(type: "string", length: 255)]
    public string $province;

    #[ORM\Column(type: "string", length: 255)]
    public string $district;

    #[ORM\Column(type: "string", length: 255)]
    public string $community;

    #[ORM\Column(type: "string", length: 255)]
    public string $zipCode;

    #[ORM\Column(type: "string", length: 255)]
    public string $city;

    #[ORM\Column(type: "string", length: 255)]
    public string $street;

    #[ORM\Column(type: "string", length: 255)]
    public string $propertyNumber;

    public function __construct(
        string $name,
        string $regon,
        string $province,
        string $district,
        string $community,
        string $zipCode,
        string $city,
        string $street,
        string $propertyNumber,
    ) {
        $this->city = $city;
        $this->name = $name;
        $this->regon = $regon;
        $this->province = $province;
        $this->district = $district;
        $this->community = $community;
        $this->zipCode = $zipCode;
        $this->street = $street;
        $this->propertyNumber = $propertyNumber;
    }

    public static function fromDomain(DomainCompany $company): self {
        return new self(
            name: $company->name,
            regon: $company->regon,
            province: $company->province,
            district: $company->district,
            community: $company->community,
            zipCode: $company->zipCode,
            city: $company->city,
            street: $company->street,
            propertyNumber: $company->propertyNumber,
        );
    }

    public function toDomain(): DomainCompany
    {
        return new DomainCompany(
            $this->name,
            $this->regon,
            $this->province,
            $this->district,
            $this->community,
            $this->zipCode,
            $this->city,
            $this->street,
            $this->propertyNumber
        );
    }
}
