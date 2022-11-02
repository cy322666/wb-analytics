<?php

namespace App\Services\Wildberries\Models;

use Illuminate\Support\Facades\Http;

class Sales
{
    const URL = 'sales';

    public function __construct($api_key)
    {
        $this->api_key = $api_key;
    }

    public function all($date)
    {
        $date = urlencode($date.'T00:00:00+03:00');

        $query = '?dateFrom='.$date.'&take=1000&skip=0&key='.$this->api_key;

        $response = Http::withHeaders($this->getHeaders())->get($this::BASE_URL.self::URL.$query);

        return json_decode($response->body(), true);
    }
}
