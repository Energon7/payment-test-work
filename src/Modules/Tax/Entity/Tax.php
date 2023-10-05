<?php

namespace App\Modules\Tax\Entity;

use App\Modules\Tax\Repository\TaxRepository;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;

#[Entity]
#[Table(name: TaxRepository::TABLE)]
class Tax
{
    #[Id]
    #[Column(type: 'integer')]
    #[GeneratedValue(strategy: 'NONE')]
    private int $id;

    #[Column(type: "string", length: 5)]
    private string $countryCode;

    #[Column(type: "integer")]
    private int $taxValue;


    public function __construct(int $id, string $countryCode, int $taxValue)
    {
        $this->id = $id;
        $this->countryCode = $countryCode;
        $this->taxValue = $taxValue;
    }

    public function getCountryCode(): string
    {
        return $this->countryCode;
    }

    public function setCountryCode(string $countryCode): void
    {
        $this->countryCode = $countryCode;
    }

    public function getTaxValue(): int
    {
        return $this->taxValue;
    }

    public function setTaxValue(int $taxValue): void
    {
        $this->taxValue = $taxValue;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public static function getCountryByTaxNumber($taxNumber): string
    {
       return strtoupper(substr($taxNumber,0,2));
    }
}