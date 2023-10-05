<?php

namespace App\Modules\Payment\Command\PayCommand;


use App\Infrastructure\Bus\CommandBus;
use App\Modules\Payment\PaymentProcessor\PaymentProcessorFactory;
use App\Modules\Product\Command\CalculatePrice\CalculatePriceCommand;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class Handler
{
    public function __construct(
        private readonly CommandBus $commandBus,
        private readonly PaymentProcessorFactory $paymentProcessorFactory
    )
    {

    }

    public function __invoke(PayCommand $command): void
    {
        $calcPriceCommand = new CalculatePriceCommand();
        $calcPriceCommand->product = $command->product;
        $calcPriceCommand->taxNumber = $command->taxNumber;
        $calcPriceCommand->couponCode = $command->couponCode;

        $calculatedPrice = $this->commandBus->command($calcPriceCommand);

        $paymentProcessor = $this->paymentProcessorFactory->createPaymentProcessor($command->paymentProcessor);
        $paymentProcessor->processPayment($calculatedPrice);
    }
}