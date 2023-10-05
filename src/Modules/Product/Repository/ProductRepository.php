<?php

namespace App\Modules\Product\Repository;


use App\Modules\Product\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use App\Infrastructure\Exception\NotFoundException;

class ProductRepository
{
    public const TABLE = 'products';
    private EntityRepository $repository;

    public function __construct(
        private readonly EntityManagerInterface $manager
    ) {
        $this->repository = $this->manager->getRepository(Product::class);
    }

    /**
     * @param int $id
     * @return Product
     * @throws NotFoundException
     */
    public function getOneById(int $id): Product
    {
        return $this->repository->find($id) ??
            throw new NotFoundException('Product not found');
    }
}