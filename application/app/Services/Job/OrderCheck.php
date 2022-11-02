<?php

namespace App\Services\Job;

use App\Models\Account;
use App\Models\Export;
use App\Services\WB\Client as WB;
use App\Services\WB\RequestDto;
use App\Services\WB\ResponseParser;
use Exception;
use GuzzleHttp\Exception\GuzzleException;

class OrderCheck
{
    private Account $account;
    private string $token;
    private ?object $options;
    private int $total;

    public function __construct(Export $export)
    {
        $this->account = $export->account;
        $this->token   = $this->account->token;
        $this->options = json_decode($export->options);
    }

    /**
     * @throws GuzzleException
     * @throws Exception
     */
    public function check(): static
    {
        $wb = WB::init($this->token);

        $request = (new RequestDto());
        $request->start = $this->options->start;
        $request->take = 1;
        $request->skip = 0;

        $response = $wb->orders()->all($request);

        $response = (new ResponseParser)->parse($response);

        $this->total = $response->total;

        return $this;
    }

    public function buildBus($job, $export) : array
    {
        for ($arrayJobs = [], $take = 500, $skip = 0; ; $skip += ++$take) {

            $arrayJobs[] = new $job($export, $take, $skip);
        }

        return $arrayJobs;
    }
}
