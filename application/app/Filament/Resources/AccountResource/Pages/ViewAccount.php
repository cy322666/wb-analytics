<?php

namespace App\Filament\Resources\AccountResource\Pages;

use App\Filament\Resources\AccountResource;
use App\Models\Account;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
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
use Illuminate\Support\Facades\Auth;

class ViewAccount extends ViewRecord
{
    protected static string $resource = AccountResource::class;

    protected static ?string $title = 'Аккаунт';

    protected function getFormSchema(): array
    {
        return [
            Group::make()
                ->schema([
                    Tabs::make('Heading')
                        ->tabs([
                            Tabs\Tab::make('Основное')
                                ->schema([
                                    TextInput::make('name')
                                        ->label('Название')
                                        ->required(),
                                    TextInput::make('token')
                                        ->label('API токен')
                                        ->required(),
                                    TextInput::make('token32')
                                        ->placeholder('API токен 32')
                                        ->required(),
                                    TextInput::make('token64')
                                        ->placeholder('API токен 64')
                                        ->required(),
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
//                            Tabs\Tab::make('Label 3')
//                                ->schema([
//                                    // ...
//                                ]),
                        ])->columnSpan(['lg' => 2]),

//                    Card::make()
//                        ->schema([
//
//                        ])
//                        ->columnSpan(['lg' => 2]),

                    Card::make()
                        ->schema([
                            Placeholder::make('created_at')
                                ->label('Created at')
                                ->content(fn (Account $record): string => $record->created_at->diffForHumans()),

                            Placeholder::make('updated_at')
                                ->label('Last modified at')
                                ->content(fn (Account $record): string => $record->updated_at->diffForHumans()),
                        ])
                        ->columnSpan(['lg' => 1]),
                ])
                ->columns(3)
//                    ->columnSpan(['lg' => 2]),
//                    ->columnSpan(['lg' => fn (?Account $record) => $record === null ? 3 : 2]),


//                    ->hidden(fn (?Account $record) => $record === null),
//            ->columns(3)
        ];
    }
}
