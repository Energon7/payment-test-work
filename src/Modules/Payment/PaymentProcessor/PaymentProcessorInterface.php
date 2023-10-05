<?php

namespace App\Modules\Payment\PaymentProcessor;

interface PaymentProcessorInterface
{
    public function processPayment(float $amount): void;
}