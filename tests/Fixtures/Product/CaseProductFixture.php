<?php

namespace Tests\Fixtures\Product;

use App\Modules\Product\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CaseProductFixture extends Fixture
{
    const ID = 3;
    const NAME = "Case";

    const PRICE = 10.0;

    public function load(ObjectManager $manager): void
    {
        $product = new Product(
            id: self::ID,
            name: self::NAME,
            price: self::PRICE
        );
        
        $manager->persist($product);
        $manager->flush();
    }
}