<?php

namespace App\Modules\Payment\PaymentProcessor;

enum PaymentProcessorEnum: string
{
    case PAYPAL = 'paypal';

    case STRIPE = 'stripe';
}
