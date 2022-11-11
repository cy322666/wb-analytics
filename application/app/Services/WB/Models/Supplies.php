<?php

namespace App\Services\WB\Models;

use App\Services\WB\Core;
use App\Services\WB\RequestDto;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

/*
 * https://openapi.wildberries.ru/#tag/Marketplace/paths/~1api~1v2~1supplies/get
 */
class Supplies
{
    const URL = 'supplies';

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
                'query' => [
                    'status' => $request->status,
                ],
                'headers'  => $this->core->headers(),
                ['timeout' => $request->timeout]
            ]);
    }
}
