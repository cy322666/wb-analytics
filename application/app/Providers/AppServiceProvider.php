<?php

namespace App\Providers;

use App\Models\Account;
use App\Models\Task;
use App\Services\Telegram\Telegram;
use Carbon\Carbon;
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

            try {
                Account::query()
                    ->where('name', $event->job->payload()['tags'][1])
                    ->first()
                    ->tasks()
                    ->where('command', $event->job->payload()['tags'][0])
                    ->where('completed', false)
                    ->first()
                    ->update([
                        'status' => 3,
                        'uuid'   => $event->job->payload()['uuid'],
                    ]);
            } catch (\Throwable $exception) {}

            $text = [
                '*Ошибка выполнения*',
                '- - - - - - - - - -',
                '*ID : *'.$event->job->payload()['uuid'],
                '*Команда : * '.$event->job->payload()['tags'][0],
                '*Клиент : * '.$event->job->payload()['tags'][1],
//                '*Текст : * '.$event->exception->getMessage(),
                '*Время : * '.Carbon::now()->format('H:i:s'),
            ];

            Telegram::send(implode("\n", $text), [
                "text" => "Детали",
                "url"  => env('APP_URL').'/telescope/jobs/'.$event->job->payload()['telescope_uuid']
            ]);
        });

        Queue::before(function (JobProcessing $event) {

            try {
                Account::query()
                    ->where('name', $event->job->payload()['tags'][1])
                    ->first()
                        ->tasks()
                        ->where('command', $event->job->payload()['tags'][0])
                        ->where('completed', false)
                        ->first()
                            ->update([
                                'status' => 1,
                                'uuid'   => $event->job->payload()['uuid'],
                            ]);
            } catch (\Throwable $exception) {}

            $text = [
                '*Старт задания*',
                '- - - - - - - - - -',
                '*ID : *'.$event->job->payload()['uuid'],
                '*Команда : *'.$event->job->payload()['tags'][0],
                '*Клиент : *'.$event->job->payload()['tags'][1],
                '*Время : * '.Carbon::now()->format('H:i:s'),
            ];

            Telegram::send(implode("\n", $text), [
                "text" => "Детали",
                "url"  => env('APP_URL').'/telescope/jobs/'.$event->job->payload()['telescope_uuid']
            ]);
        });

        Queue::after(function (JobProcessed $event) {

            try {
                Task::query()
                    ->where('uuid', $event->job->payload()['uuid'])
                    ->first()
                        ->update([
                            'completed' => true,
                            'status' => 2,
                        ]);
            } catch (\Throwable $exception) {}

            $text = [
                '*Завершение задания*',
                '- - - - - - - - - -',
                '*ID : *'.$event->job->payload()['uuid'],
                '*Команда : * '.$event->job->payload()['tags'][0],
                '*Клиент : * '.$event->job->payload()['tags'][1],
                '*Время : * '.Carbon::now()->format('H:i:s'),
            ];

            Telegram::send(implode("\n", $text), [
                "text" => "Детали",
                "url"  => env('APP_URL').'/telescope/jobs/'.$event->job->payload()['telescope_uuid']
            ]);
        });
    }
}
