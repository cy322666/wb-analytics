<?php

namespace App\Filament\Resources\AccountResource\Pages;

use App\Filament\Resources\AccountResource;
use App\Models\Account;
use Filament\Forms\ComponentContainer;
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

class ViewAccount extends ViewRecord implements HasTable
{
    use InteractsWithTable;

    protected static string $resource = AccountResource::class;

    public $record;

    protected function getTableQuery(): Builder
    {
        return Account::query()->find($this->record)->get();
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('token'),
            TextColumn::make('created_at')->sortable(),
            TextColumn::make('last_updated_at')->sortable(),
        ];
    }

    protected function getTableFilters(): array
    {
        return [];
    }

    protected function getTableActions(): array
    {
        return [];
    }

    protected function getTableBulkActions(): array
    {
        return [];
    }
}
