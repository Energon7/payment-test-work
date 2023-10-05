<?php

namespace App\Modules\Payment\PaymentProcessor;

class PaymentProcessorFactory
{
    public function __construct(
        private readonly PaypalPaymentProcessor $paypalPaymentProcessor,
        private readonly StripePaymentProcessor $stripePaymentProcessor
    ) {
    }

    public function createPaymentProcessor(PaymentProcessorEnum $paymentProcessor): PaymentProcessorInterface
    {
        return match ($paymentProcessor) {
            PaymentProcessorEnum::PAYPAL => $this->paypalPaymentProcessor,
            PaymentProcessorEnum::STRIPE => $this->stripePaymentProcessor,
        };
    }
}