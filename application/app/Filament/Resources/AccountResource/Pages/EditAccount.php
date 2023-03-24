<?php

namespace App\Filament\Resources\AccountResource\Pages;

use App\Filament\Resources\AccountResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Artisan;

class EditAccount extends EditRecord
{
    protected static string $resource = AccountResource::class;

    protected function getActions(): array
    {
        return [
            Actions\Action::make('reloadAll')
                ->label('Выгрузить все')
                ->action('reloadAll'),

            Actions\Action::make('deleteAll')
                ->label('Удалить все')
                ->action('deleteAll'),

            Actions\Action::make('truncateAll')
                ->label('Отчистить БД')
                ->action('truncateAll'),

            Actions\DeleteAction::make(),
        ];
    }

    public function truncateAll()
    {
        Artisan::call('wb:truncate-all '.$this->getRecord()->id);
    }

    public function reloadAll()
    {
        Artisan::call('wb:reload-all '.$this->getRecord()->id);
    }
}
