<?php

namespace App\Modules\Payment\PaymentProcessor;
use App\Infrastructure\Exception\Validation\ValidationException;
use Systemeio\TestForCandidates\PaymentProcessor\StripePaymentProcessor as Stripe;
class StripePaymentProcessor implements PaymentProcessorInterface
{
    public function processPayment(float $amount): void
    {
        $payment = new Stripe();
        $result = $payment->processPayment($amount);

        if (!$result) {
            throw new ValidationException(null, message: 'Payment was declined. Price too small',);
        }
    }
}