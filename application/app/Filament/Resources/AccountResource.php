<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AccountResource\Pages;
use App\Filament\Resources\AccountResource\RelationManagers;
use App\Models\Account;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AccountResource extends Resource
{
    protected static ?string $model = Account::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()
                    ->schema([
                        Tabs::make('Heading')
                            ->tabs([
                                Tabs\Tab::make('Основное')
                                    ->schema([
                                        TextInput::make('name')
                                            ->label('Название')
                                            ->required(),
                                        TextInput::make('token_standard')
                                            ->label('API токен стандарт')
                                            ->required(),
                                        TextInput::make('token_statistic')
                                            ->label('API токен статистики')
                                            ->required(),
                                        TextInput::make('token_adv')
                                            ->label('API токен рекламы'),
                                    ]),
                                Tabs\Tab::make('База данных')
                                    ->schema([
                                        TextInput::make('db_host')
                                            ->label('IP')
                                            ->required(),
                                        TextInput::make('db_username')
                                            ->label('Логин')
                                            ->required(),
                                        TextInput::make('db_password')
                                            ->label('Пароль')
                                            ->required(),
                                        TextInput::make('db_port')
                                            ->label('Порт')
                                            ->required(),
                                        TextInput::make('db_type')
                                            ->label('Тип')
                                            ->required(),
                                        TextInput::make('db_name')
                                            ->placeholder('База данных')
                                            ->required(),
                                    ]),
                            ])->columnSpan(['lg' => 2]),
                        Card::make()
                            ->schema([
                                Placeholder::make('created_at')
                                    ->label('Создано')
                                    ->content(fn (Account $record): string => $record->created_at->diffForHumans()),

                                Placeholder::make('updated_at')
                                    ->label('Обновлено')
                                    ->content(fn (Account $record): string => $record->updated_at->diffForHumans()),

                                TimePicker::make('time_load')
                                    ->label('Время выгрузки')
                                    ->required(),
                            ])
                            ->columnSpan(['lg' => 1]),
                    ])
                    ->columns(3)
            ]);
    }

    public static function getRelations(): array
    {
        return [
            AccountResource\RelationManagers\WbExportsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAccounts::route('/'),
            'create' => Pages\CreateAccount::route('/create'),
            'view' => Pages\ViewAccount::route('/{record}'),
            'edit' => Pages\EditAccount::route('/{record}/edit'),
        ];
    }
}
