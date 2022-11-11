<?php

namespace App\Services\WB;

use App\Services\WB\Models\Orders;
use GuzzleHttp\Client;

/* @method Orders orders() */

class Core
{
    public Client $http;

    public string $token;
    public string $token32;
    public string $token64;

    public function init(
        string $token,
        string $token32,
        string $token64,
    ): Core
    {
        $this->http = new Client();

        $this->token   = $token;
        $this->token32 = $token32;
        $this->token64 = $token64;

        return $this;
    }

    public function __call($name, $arguments)
    {
        if (count($arguments) > 0 && $arguments[0] == 'supplie') {

            $modelName = __NAMESPACE__.'\\Models\\Supplie\\'.ucfirst($name);
        } else
            $modelName = __NAMESPACE__.'\\Models\\'.ucfirst($name);

        return $this->$name = new $modelName($this);
    }

    public function headers() : array
    {
        return [
            'accept' => 'application/json',
            'Authorization' => $this->token,
        ];
    }

    public function headersStats() : array
    {
        return [
            'accept' => 'application/json',
        ];
    }
}
