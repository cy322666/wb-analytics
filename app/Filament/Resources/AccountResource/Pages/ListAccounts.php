<?php

namespace App\Filament\Resources\AccountResource\Pages;

use App\Filament\Resources\AccountResource;
use App\Models\Account;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ListAccounts extends ListRecords
{
    protected static string $resource = AccountResource::class;

    protected function getTableQuery(): Builder
    {
        return Account::query();
    }

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function table(\Filament\Resources\Table $table): \Filament\Resources\Table
    {
        return $table
            ->columns([
                TextColumn::make('token'),
                TextColumn::make('created_at')->sortable(),
                TextColumn::make('last_updated_at')->sortable(),
            ])
            ->defaultSort('created_at');
    }
}
