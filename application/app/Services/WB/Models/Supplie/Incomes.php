<?php

namespace App\Services\WB\Models\Supplie;

use App\Services\WB\Core;
use App\Services\WB\RequestDto;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;

class Incomes
{
    const URL = 'incomes';

    public const BASE_URL = 'https://suppliers-stats.wildberries.ru/api/v1/supplier/';

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
                    'key' => $this->core->token64,
                    'dateFrom' => $request->dateFrom,
                    'flag' => $request->flag,
                ],
                'headers'  => $this->core->headersStats(),
                ['timeout' => $request->timeout]
            ]);
    }
}
