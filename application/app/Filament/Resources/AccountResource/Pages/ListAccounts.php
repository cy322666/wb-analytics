<?php

namespace App\Filament\Resources\AccountResource\Pages;

use App\Filament\Resources\AccountResource;
use App\Models\Account;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Table;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;

class ListAccounts extends ListRecords
{
    protected static string $resource = AccountResource::class;

    protected static ?string $title = 'Аккаунты';

    protected function getTableQuery(): Builder
    {
        return Account::query();
    }

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make()->label('+Новый'),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Название'),
                TextColumn::make('created_at')->sortable()->label('Создан'),
                TextColumn::make('last_updated_at')->sortable()->label('Обновлен'),
            ])
            ->defaultSort('created_at')
            ->actions([
                ViewAction::make()->label('Просмотр'),
                EditAction::make()->label('Изменить'),
            ]);
    }
}
