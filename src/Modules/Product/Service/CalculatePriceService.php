<?php

namespace App\Modules\Product\Service;

use App\Modules\Coupon\Entity\Coupon;
use App\Modules\Coupon\Entity\DiscountType;
use App\Modules\Tax\Entity\Tax;

class CalculatePriceService
{
    public function calculate(float $price, $taxValue, ?Coupon $coupon = null): float
    {
        if ($coupon) {
            $price = $this->getDiscountedPrice(
                price: $price,
                discountType: $coupon->getDiscountType(),
                discountValue: $coupon->getDiscountValue()
            );
        }

        return $this->calculateTax($price, $taxValue);
    }

    /**
     * @param float $price
     * @param DiscountType $discountType
     * @param float $discountValue
     * @return float
     */
    private function getDiscountedPrice(float $price, DiscountType $discountType, float $discountValue): float
    {

        return match ($discountType) {
            DiscountType::PERCENT => $price - round(($price * $discountValue) / 100, 2),
            DiscountType::VALUE => $price - $discountValue,
        };
    }

    /**
     * @param float $price
     * @param int $taxPercent
     * @return float
     */
    private function calculateTax(float $price, int $taxPercent): float
    {
        return round(($price * $taxPercent / 100) + $price, 2);
    }
}