<?php

namespace App\Enums;

enum PaymentChannel: string
{
    case KCB = 'KENYA COMMERCIAL BANK';
    case Vooma = 'VOOMA MAKONGENI';
    case Equity = 'EQUITY BANK';
    case Eazzy = 'EQUITY MAKONGENI';
    case Mpesa = 'MPESA PAYBILL';
}
