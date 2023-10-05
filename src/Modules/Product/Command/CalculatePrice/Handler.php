<?php

namespace App\Modules\Product\Command\CalculatePrice;


use App\Modules\Coupon\Repository\CouponRepository;
use App\Modules\Product\Repository\ProductRepository;
use App\Modules\Product\Service\CalculatePriceService;
use App\Modules\Tax\Repository\TaxRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class Handler
{

    public function __construct(
        private readonly ProductRepository $productRepository,
        private readonly CouponRepository $couponRepository,
        private readonly TaxRepository $taxRepository,
        private readonly CalculatePriceService $calculatePriceService,
    )
    {

    }

    public function __invoke(CalculatePriceCommand $command): float
    {
        $product = $this->productRepository->getOneById($command->product);

        if ($command->couponCode) {
            $coupon = $this->couponRepository->findOneByCode($command->couponCode);
        }

        $tax = $this->taxRepository->getTaxValueByCode($command->taxNumber);

        return $this->calculatePriceService->calculate(
            price: $product->getPrice(),
            taxValue: $tax->getTaxValue(),
            coupon: $coupon ?? null
        );
    }
}