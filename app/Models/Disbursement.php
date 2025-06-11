<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Disbursement extends Model
{
    use HasFactory;

    protected $guarded = [];

    static public function buildDocumentNumber($id): string
    {
        $prefix = match (true) {
            $id < 10 => '00000',
            ($id >= 10) && ($id < 100) => '0000',
            ($id >= 100) && ($id < 1000) => '000',
            ($id >= 1000) && ($id < 10000) => '00',
            ($id >= 10000) && ($id < 100000) => '0',
            default => '',
        };

        return "DSB-$prefix$id";
    }
}
