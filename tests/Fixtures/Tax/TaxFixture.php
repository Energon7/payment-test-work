<?php

namespace Tests\Fixtures\Tax;

use App\Modules\Tax\Entity\Tax;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TaxFixture extends Fixture
{

    public function load(ObjectManager $manager): void
    {
       $tax1 = new Tax(id: 1, countryCode: 'DE', taxValue: 19);
       $tax2 = new Tax(id: 2, countryCode: 'IT', taxValue: 22);
       $tax3 = new Tax(id: 3, countryCode: 'FR', taxValue: 20);
       $tax4 = new Tax(id: 4, countryCode: 'GR', taxValue: 24);

       $manager->persist($tax1);
       $manager->persist($tax2);
       $manager->persist($tax3);
       $manager->persist($tax4);

       $manager->flush();
    }
}