<?php

namespace App\Providers;

use App\Services\Telegram\Telegram;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Queue::failing(function (JobFailed $event) {

            Log::error(__METHOD__, [
                $event->connectionName,
                $event->job->getName(),
                $event->exception->getMessage(),
            ]);

            Telegram::send(   $event->job->getName().' : '.$event->exception->getMessage());
        });

        Queue::before(function (JobProcessing $event) {

            Log::info(__METHOD__, [
                 $event->connectionName,
                 $event->job,
                 $event->job->payload(),
            ]);

            Telegram::send('Старт задания '.$event->job->getQueue().'...');
        });

        Queue::after(function (JobProcessed $event) {

            Log::info(__METHOD__, [
                $event->connectionName,
                $event->job,
                $event->job->payload(),
            ]);

            Telegram::send('Конец задания...');
        });
    }
}
