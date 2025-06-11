<?php

namespace App\Enums;

enum PromotionMatrix:string
{
    case BSGY = 'Buy X Get Y Free';
    case PD = 'Price Discount. X was N now N-1';
    case HAMPER = 'Hampers : Grouped products';
}
