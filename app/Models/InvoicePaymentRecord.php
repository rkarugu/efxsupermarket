<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoicePaymentRecord extends Model
{
    use HasFactory;
    protected $table = 'invoice_payment_records';
}
