<?php

use App\Services\ModelCompatibilityService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->describe('Display an inspiring quote');

Artisan::command('model_compatibility:update', function () {
    $this->comment('Starting script');

    $response = (new ModelCompatibilityService())->update();
    if ($response['success']) {
        $this->info('Success!');
    } else {
        $this->error('Script fail: ' . $response['message']);
    }
})->describe('Update models with new functionality');
