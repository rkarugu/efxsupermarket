<?php

namespace App\Interfaces;

interface SmsService
{
    public function sendMessage(string $msg, string $phoneNumber): void;
    public function sendOtp(string $msg, string $phoneNumber): void;
}