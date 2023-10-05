<?php

namespace App\Modules\Product\Entity;

use App\Modules\Product\Repository\ProductRepository;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;

#[Entity]
#[Table(name: ProductRepository::TABLE)]
class Product
{
    #[Id]
    #[Column(type: 'integer')]
    #[GeneratedValue(strategy: 'NONE')]
    private int $id;

    #[Column(type: "string", length: 255)]
    private string $name;



    #[Column(type: "decimal", precision:10, scale: 2)]
    private float $price;


    public function __construct(
        int $id,
        string $name,
        float $price
    )
    {
        $this->id = $id;
        $this->name = $name;
        $this->price = $price;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): void
    {
        $this->price = $price;
    }
}