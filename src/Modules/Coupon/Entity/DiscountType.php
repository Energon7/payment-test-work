<?php

namespace App\Modules\Coupon\Entity;

enum DiscountType: string
{
    case PERCENT = 'percent';
    case VALUE = 'value';
}
