<?php

namespace App\Services\Wildberries\Models;

use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class Reports
{
    const URL = 'reportDetailByPeriod';

    public function __construct($api_key)
    {
        $this->api_key = $api_key;
    }

    public function all(string $date, string $date_to)
    {
        $query = 'dateFrom='.$date.'&dateto='.$date_to.'&limit=1000&rrdid=0&&key='.$this->api_key;//take=1000&skip=0&key='.

        //for ($i = 0; ; $i++) {

        $response = Http::withHeaders($this->getHeaders())->get($this::BASE_URL.self::URL.'?'.$query);

        return json_decode($response->body());
       // }
    }

    public function get(string $date, string $date_to, $rrdid)
    {
        $query = 'dateFrom='.$date.'&dateto='.$date_to.'&limit=1000&rrdid='.$rrdid.'&&key='.$this->api_key;//take=1000&skip=0&key='.

        //for ($i = 0; ; $i++) {

        $response = Http::withHeaders($this->getHeaders())->get($this::BASE_URL.self::URL.'?'.$query);

        return json_decode($response->body());
    }
}
