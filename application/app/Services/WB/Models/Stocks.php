<?php

namespace App\Services\WB\Models;

use App\Services\WB\Core;
use App\Services\WB\RequestDto;

/*
 * https://openapi.wildberries.ru/#tag/Marketplace/paths/~1api~1v2~1supplies~1%7Bid%7D~1orders/get
 */
class Stocks
{
    const URL = 'stocks';

    public const BASE_URL = 'https://suppliers-api.wildberries.ru/api/v2/';

    public function __construct(private Core $core) {}

    public function all(RequestDto $request)
    {
        return $this->core
            ->http
            ->get(self::BASE_URL.self::URL, [
                'query' => [
                    'take' => $request->take,
                    'skip' => $request->skip,
                ],
                'headers'  => $this->core->headers(),
                ['timeout' => $request->timeout]
            ]);
    }
}
