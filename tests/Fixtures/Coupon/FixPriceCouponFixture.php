<?php

namespace Tests\Fixtures\Coupon;

use App\Modules\Coupon\Entity\Coupon;
use App\Modules\Coupon\Entity\DiscountType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class FixPriceCouponFixture extends Fixture
{
    const ID = 2;
    const CODE = 'D15FIX';


    public function load(ObjectManager $manager): void
    {
        $product = new Coupon(
            id: self::ID,
            code: self::CODE,
            discountType: DiscountType::VALUE,
            discountValue: 24
        );
        
        $manager->persist($product);
        $manager->flush();
    }
}