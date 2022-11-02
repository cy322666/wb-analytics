<?php

namespace App\Services\WB\Models;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class Incomes
{
    const URL = 'incomes';

    public function __construct($api_key)
    {
        $this->api_key = $api_key;
    }

    public function all(string $date)
    {
        $date = urlencode($date.'T00:00:00+03:00');

        $query = 'dateFrom='.$date.'&key='.$this->api_key;//take=1000&skip=0&key='.

        //for ($i = 0; ; $i++) {

        $response = Http::withHeaders($this->getHeaders())->get($this::BASE_URL.self::URL.'?'.$query);

        return json_decode($response->body());
       // }
    }
}
