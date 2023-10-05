<?php

namespace App\Modules\Payment\PaymentProcessor;
use App\Infrastructure\Exception\Validation\ValidationException;
use \Systemeio\TestForCandidates\PaymentProcessor\PaypalPaymentProcessor as PayPal;
class PayPalPaymentProcessor implements PaymentProcessorInterface
{
    public function processPayment(float $amount): void
    {
        try {
            $payment = new PayPal();
            $payment->pay($amount);
        }catch (\Exception $exception) {
            throw new ValidationException(null, message: $exception->getMessage());
        }
    }
}