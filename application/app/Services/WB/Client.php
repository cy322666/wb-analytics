<?php

namespace App\Services\WB;

abstract class Client
{
    public static function init(
        string $token,
        string $token32,
        string $token64,
    ): Core
    {
        return (new Core())->init($token, $token32, $token64);
    }
}
