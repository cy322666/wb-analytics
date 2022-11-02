<?php

namespace App\Services\WB;

abstract class Client
{
    public static function init(string $token): Core
    {
        return (new Core())->init($token);
    }
}
