<?php

namespace App\Services\WB;

use Exception;
use Psr\Http\Message\ResponseInterface;

class ResponseParser
{
    /**
     * @throws Exception
     */
    public function parse(ResponseInterface $response)
    {
        if ($response->getStatusCode() == 200) {

            if ($response->getBody()) {

                return json_decode($response->getBody()->getContents());
            }
        } else
            throw new Exception('exception response');
    }
}
