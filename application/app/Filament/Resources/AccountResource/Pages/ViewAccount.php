<?php

namespace App\Filament\Resources\AccountResource\Pages;

use App\Filament\Resources\AccountResource;
use App\Models\Account;
use Faker\Core\Color;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\Layout\Component;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;

class ViewAccount extends ViewRecord
{
    protected static string $resource = AccountResource::class;

    protected static ?string $title = 'Аккаунт';

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
        ];
    }

    public function reloadAll()
    {
        Artisan::call('wb:reload-all '.$this->getRecord()->id);
    }

    public function truncateAll()
    {
        Artisan::call('wb:truncate-all '.$this->getRecord()->id);
    }
}
