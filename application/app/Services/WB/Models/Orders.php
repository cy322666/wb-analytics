<?php

namespace App\Services\WB\Models;

use App\Services\WB\Core;
use App\Services\WB\RequestDto;
use GuzzleHttp\Exception\GuzzleException;

class Orders
{
    const URL = 'orders';

    public function __construct(private Core $core) {}

    /**
     * @throws GuzzleException
     */
    public function all(RequestDto $request)
    {
        return $this->core
            ->http
            ->get(Core::BASE_URL.Orders::URL, [
                'query' => [
                    'date_start' => $request->start,
                    'date_end' => $request->end,
                    'take' => $request->take,
                    'skip' => $request->skip,
                ],
                'headers'  => $this->core->headers(),
                ['timeout' => $request->timeout]
            ]);
    }

//        if($count_days > 1) {
//
//            $array = [];
//
//            $date = Carbon::parse($date);
//
//            for ($i = 1; $i != $count_days; $i++) {
//
//                $response = Http::withHeaders($this->getHeaders())->get($this::BASE_URL.self::URL.'?dateFrom='.$date.'&key='.$this->api_key);
//
//                if(json_decode($response->body())) {
//
//                    $array = array_merge($array, json_decode($response->body()));
//
//                    $date = $date->addDay()->format('Y-m-d');
//                }
//
//                return $array;
//            }
//
//        } else
//            $response = Http::withHeaders($this->getHeaders())->get($this::BASE_URL.self::URL.$query);
//
//        return json_decode($response->body());
}
