<?php
namespace App\Modules\Product\Command\CalculatePrice;

use Symfony\Component\Validator\Constraints as Assert;
final class CalculatePriceCommand
{

    #[Assert\NotBlank]
    #[Assert\Positive]
    public int $product;

    #[Assert\NotBlank]
    public string $taxNumber;

    public ?string $couponCode = null;
}