<?php

namespace App\Observers;

use App\Models\Account;
use Illuminate\Support\Facades\Artisan;

class AccountObserver
{
    //запускать после завершения транзакции
    public bool $afterCommit = true;

    /**
     * Handle the Account "created" event.
     *
     * @param Account $account
     * @return void
     */
    public function created(Account $account)
    {
        Artisan::call('wb:install '. $account->id);
    }

    /**
     * Handle the Account "updated" event.
     *
     * @param Account $account
     * @return void
     */
    public function updated(Account $account)
    {
        //
    }

    /**
     * Handle the Account "deleted" event.
     *
     * @param Account $account
     * @return void
     */
    public function deleted(Account $account)
    {
        //
    }

    /**
     * Handle the Account "restored" event.
     *
     * @param Account $account
     * @return void
     */
    public function restored(Account $account)
    {
        //
    }

    /**
     * Handle the Account "force deleted" event.
     *
     * @param Account $account
     * @return void
     */
    public function forceDeleted(Account $account)
    {
        //
    }
}
