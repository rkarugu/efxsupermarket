<?php

namespace App;

use Illuminate\Support\Facades\Crypt;

class EncryptionHelper
{
  public static function encrypt($data)
  {
    $key = env('ENCRYPTION_KEY');
    return Crypt::encrypt($data, $key);
  }

  public static function decrypt($encryptedData)
  {
    $key = env('ENCRYPTION_KEY');
    return Crypt::decrypt($encryptedData, $key);
  }
}
