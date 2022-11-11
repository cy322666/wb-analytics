<?php

namespace App\Services\WB\Models;

use App\Services\WB\Core;
use App\Services\WB\RequestDto;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

/*
 * https://openapi.wildberries.ru/#tag/Marketplace/paths/~1api~1v2~1warehouses/get
 */
class Warehouses
{
    const URL = 'warehouses';

    public const BASE_URL = 'https://suppliers-api.wildberries.ru/api/v2/';

    public function __construct(private Core $core) {}

    /**
     * @throws GuzzleException
     */
    public function all(RequestDto $request): ResponseInterface
    {
        return $this->core
            ->http
            ->get(self::BASE_URL.self::URL, [
                'query' => [],
                'headers'  => $this->core->headers(),
                ['timeout' => $request->timeout]
            ]);
    }
}
