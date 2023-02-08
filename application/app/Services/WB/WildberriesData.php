<?php

namespace App\Services\WB;

class WildberriesData
{
    public $data;
    public $status;

    public function __construct(WildberriesResponse $response)
    {
        $data = $response->toSimpleObject();
        $this->data = $data->data;
        $this->status = $data->status;
    }
}
