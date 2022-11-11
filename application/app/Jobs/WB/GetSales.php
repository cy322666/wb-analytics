<?php

namespace App\Jobs\WB;

use App\Models\Export;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class GetSales implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;

    public int $maxExceptions = 1;

    public int $timeout = 30;

    public bool $failOnTimeout = true;

    public function __construct(
        private Export $export,
        private int $take,
        private int $skip,
    ) {}

    public function handle()
    {
        $result = Artisan::call("wb:orders {$this->export->id}");

        Log::info(__METHOD__.' : queue result : ', [$result]);

        $this->export->finish_at = Carbon::now()->format('Y-m-d H:i:s');
        $this->export->status = 2;
        $this->export->save();
    }
}
