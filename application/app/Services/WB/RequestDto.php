<?php

namespace App\Services\WB;

class RequestDto
{
    public string $start;
    public string $dateFrom;
    public string $dateTo;
    public ?string $end = null;
    public int $take = 1000;
    public int $limit = 1000;
    public int $skip = 0;
    public int $flag = 0;
    public int $rrdId = 0;
    public string $status = 'ACTIVE';//ON_DELIVERY

    public int $timeout = 2;
}
