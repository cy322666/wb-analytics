<?php

namespace App\Providers;

use App\Models\Account;
use App\Observers\AccountObserver;
use App\Services\Telegram\Telegram;
use Filament\Notifications\Notification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Queue\Events\QueueBusy;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;

class EventServiceProvider extends ServiceProvider
{
    protected $observers = [
        Account::class => [AccountObserver::class],
    ];

    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        Event::listen(function (QueueBusy $event) {
//            Notification::route('mail', 'dev@example.com')
              //TODO push tg

            Log::error('eventservice_error', [
                $event->connection,
                $event->queue,
                $event->size
            ]);

            Telegram::send(__METHOD__.' '.$event->connection);
        });
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
