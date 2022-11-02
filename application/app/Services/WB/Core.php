<?php

namespace App\Services\WB;

use App\Services\WB\Models\Orders;
use GuzzleHttp\Client;

/* @method Orders orders() */

class Core
{
    public const BASE_URL = 'https://suppliers-api.wildberries.ru/api/v2/';

    public Client $http;

    private string $token;

    public function init(string $token): Core
    {
        $this->http = new Client();

        $this->token = $token;

        return $this;
    }

    public function __call($name, $arguments)
    {
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
}
