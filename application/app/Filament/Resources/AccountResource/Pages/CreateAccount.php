<?php

namespace App\Filament\Resources\AccountResource\Pages;

use App\Filament\Resources\AccountResource;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateAccount extends CreateRecord implements HasForms
{
    use InteractsWithForms;

    protected static string $resource = AccountResource::class;

    public function mount(): void
    {
        $this->form->fill();
    }

    protected function getFormSchema(): array
    {
        return [
            Section::make('Настройки')
                ->description('Создайте БД или подключите свою')
                ->schema([
                    Card::make([
                        TextInput::make('name')
                            ->label('Название')
                            ->helperText('Чтобы удобно было различать (Например: название клиента)')
                            ->required(),
                        TextInput::make('token_standard')
                            ->label('API токен стандарт')
                            ->required(),
                        TextInput::make('token_statistic')
                            ->label('API токен статистики')
                            ->required(),
                        TextInput::make('token_adv')
                            ->label('API токен рекламы'),
                        TextInput::make('db_name')
                            ->label('Название Базы')
                            ->helperText('Если нет готовой будет создана с этим названием')
                            ->required(),
                    ])->columnSpan([
                        'sm' => 2,
                    ]),

                Card::make()
                    ->schema([

                        Toggle::make('is_remote')
                            ->label('Своя БД')
                            ->default(false)
                            ->reactive()
                            ->helperText('Если нужно выгрузить в вашу Базу')
                            ->afterStateUpdated(function ($state, callable $set) {

                                $set('db_host', null);
                                $set('db_username', null);
                                $set('db_password', null);
                                $set('db_type', null);
                                $set('db_port', null);
                            })
                            ->inline(),
                        TextInput::make('db_host')
                            ->label('IP адрес')
                            ->reactive()
                            ->required()
                            ->default('postgresql'),
                        TextInput::make('db_username')
                            ->label('Имя пользователя')
                            ->reactive()
                            ->required()
                            ->default('root'),
                        TextInput::make('db_password')
                            ->label('Пароль')
                            ->reactive()
                            ->required()
                            ->default('pQLkm8NOkS0gOBox'),
                        Select::make('db_type')
                            ->label('Драйвер')
                            ->options([
                                'mysql' => 'mysql',
                                'pgsql' => 'pgsql',
                            ])
                            ->reactive()
                            ->required()
                            ->default('pgsql'),
                        TextInput::make('db_port')
                            ->label('Порт')
                            ->reactive()
                            ->required()
                            ->default('5432'),

                        Hidden::make('user_id')
                            ->default(Auth::user()->id),

                    ])->columnSpan(1),
            ])
            ->columns([
                'sm' => 3,
                'lg' => null,
            ])
        ];
    }
}
