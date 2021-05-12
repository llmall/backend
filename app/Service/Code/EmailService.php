<?php

namespace App\Service\Code;

class EmailService implements SendCodeInterface
{

    public static function sendMsg($account)
    {
        $code = rand(100000,999999);
        print_r($code);
        return true;
    }
}