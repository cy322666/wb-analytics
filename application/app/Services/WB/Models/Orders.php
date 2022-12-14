<?php

namespace App\Services\WB\Models;

use App\Services\WB\Core;
use App\Services\WB\RequestDto;
use GuzzleHttp\Exception\GuzzleException;

class Orders
{
    const URL = 'orders';

    public const BASE_URL = 'https://suppliers-api.wildberries.ru/api/v2/';

    public function __construct(private Core $core) {}

    /**
     * @throws GuzzleException
     */
    public function all(RequestDto $request)
    {
        return $this->core
            ->http
            ->get(self::BASE_URL.self::URL, [
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
}
