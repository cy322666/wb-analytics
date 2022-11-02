<?php

namespace App\Services\WB;

class RequestDto
{
    public string $start;
    public ?string $end = null;
    public int $take = 1000;
    public int $skip = 0;

    public int $timeout = 2;
}
