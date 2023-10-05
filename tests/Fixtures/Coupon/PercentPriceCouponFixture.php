<?php

namespace Tests\Fixtures\Coupon;

use App\Modules\Coupon\Entity\Coupon;
use App\Modules\Coupon\Entity\DiscountType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class PercentPriceCouponFixture extends Fixture
{
    const ID = 1;
    const CODE = 'D15';

    public function load(ObjectManager $manager): void
    {
        $product = new Coupon(
            id: self::ID,
            code: self::CODE,
            discountType: DiscountType::PERCENT,
            discountValue: 6
        );
        
        $manager->persist($product);
        $manager->flush();
    }
}