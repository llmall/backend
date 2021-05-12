<?php

namespace App\Service\Code;

interface SendCodeInterface
{
    public static function sendMsg($account);
}