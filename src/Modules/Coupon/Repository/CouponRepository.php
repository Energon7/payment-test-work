<?php

namespace App\Modules\Coupon\Repository;


use App\Modules\Coupon\Entity\Coupon;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class CouponRepository
{
    public const TABLE = 'coupons';
    private EntityRepository $repository;

    public function __construct(
        private readonly EntityManagerInterface $manager
    ) {
        $this->repository = $this->manager->getRepository(Coupon::class);
    }

    /**
     * @param string $code
     * @return Coupon|null
     */
    public function findOneByCode(string $code): ?Coupon
    {
        return $this->repository->findOneBy([
            'code' => $code
        ]);
    }
}