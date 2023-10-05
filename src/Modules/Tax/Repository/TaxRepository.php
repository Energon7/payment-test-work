<?php

namespace App\Modules\Tax\Repository;

use App\Infrastructure\Exception\NotFoundException;
use App\Modules\Tax\Entity\Tax;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class TaxRepository
{
    public const TABLE = 'taxes';
    private EntityRepository $repository;

    public function __construct(
        private readonly EntityManagerInterface $manager
    ) {
        $this->repository = $this->manager->getRepository(Tax::class);
    }

    public function getTaxValueByCode($taxNumber)
    {
        $code = Tax::getCountryByTaxNumber($taxNumber);
        return $this->repository->findOneBy(['countryCode' => $code]) ??
            throw new NotFoundException('Country with this tax number not found');
    }

}