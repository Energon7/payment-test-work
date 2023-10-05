<?php
namespace App\Modules\Payment\Command\PayCommand;

use App\Modules\Payment\PaymentProcessor\PaymentProcessorEnum;
use Symfony\Component\Validator\Constraints as Assert;

final class PayCommand
{

    #[Assert\NotBlank]
    #[Assert\Positive]
    public int $product;

    //TODO need validate tax number count
    #[Assert\NotBlank]
    public string $taxNumber;

    public ?string $couponCode = null;

    #[Assert\NotBlank]
    public PaymentProcessorEnum $paymentProcessor;

}