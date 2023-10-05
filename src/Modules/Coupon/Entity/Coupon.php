<?php

namespace App\Modules\Coupon\Entity;

use App\Modules\Coupon\Repository\CouponRepository;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;

#[Entity]
#[Table(name: CouponRepository::TABLE)]
class Coupon
{
    #[Id]
    #[Column(type: 'integer')]
    #[GeneratedValue(strategy: 'NONE')]
    private int $id;

    #[Column(type: "string", length: 255)]
    private string $code;


    #[Column(type: "string", length: 255, enumType: DiscountType::class)]
    private DiscountType $discountType;

    #[Column(type: "decimal", precision:10, scale: 2)]
    private float $discountValue;


    public function __construct(
        int $id,
        string $code,
        DiscountType $discountType,
        float $discountValue,
    )
    {
        $this->id = $id;
        $this->code = $code;
        $this->discountType = $discountType;
        $this->discountValue = $discountValue;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getDiscountType(): DiscountType
    {
        return $this->discountType;
    }

    public function setDiscountType(DiscountType $discountType): void
    {
        $this->discountType = $discountType;
    }

    public function getDiscountValue(): float
    {
        return $this->discountValue;
    }

    public function setDiscountValue(float $discountValue): void
    {
        $this->discountValue = $discountValue;
    }
}