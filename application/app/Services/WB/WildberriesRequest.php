<?php

namespace App\Services\WB;

use Exception;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Psr\Http\Message\ResponseInterface;
use KFilippovk\Wildberries\Exceptions\WildberriesHttpException;
use Throwable;

class WildberriesRequest
{
    private Client $httpClient;

    public function __construct()
    {
        $this->httpClient = new Client();
    }

    /**
     * Create POST/GET request to WB API
     *
     * @param string $url
     * @param array $options
     * @param string $method
     * @return ResponseInterface
     * @throws Exception
     */

    public function makeRequest(string $url, array $options, string $method = 'get'): ResponseInterface
    {
        return $method === 'get' ? $this->runGetRequest($url, $options) : $this->runPostRequest($url, $options);
    }

    /**
     * Create GET request to WB API
     *
     * @param string $url
     * @param array $options
     * @return ResponseInterface
     * @throws Exception
     */
    private function runGetRequest(string $url, array $options): ResponseInterface
    {
        try {
            Log::channel('request')->info($url, $options['query'] ?? []);

            return $this->httpClient->get($url, $options);
        }
        catch (Throwable $exception) {

            throw new Exception($exception);
        }
    }

    /**
     * Create POST request to WB API
     *
     * @param string $url
     * @param array $options
     * @return ResponseInterface
     * @throws Exception
     */
    private function runPostRequest(string $url, array $options): ResponseInterface
    {
        try {
            return $this->httpClient->post($url, $options);
        } catch (Throwable $exception) {
            throw new Exception($exception);
        }
    }
}
