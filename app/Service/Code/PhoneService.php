<?php

namespace App\Service\Code;

class PhoneService implements SendCodeInterface
{

    public static function sendMsg($account): bool
    {
        $code = rand(100000,999999);
        print_r($code);
        return true;
    }
}