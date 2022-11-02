<?php

namespace App\Filament\Resources\AccountResource\Pages;

use App\Filament\Resources\AccountResource;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
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
            TextInput::make('name')
                ->placeholder('Название аккаунта')
                ->required(),
            TextInput::make('token')
                ->placeholder('API токен')
                ->required(),
            TextInput::make('db_name')
                ->placeholder('Название Базы')
                ->required(),
            TextInput::make('db_name')
                ->placeholder('База данных')
                ->required(),

            Hidden::make('user_id')
                ->default(Auth::user()->id),
            Hidden::make('db_host')
                ->default('postgresql'),
            Hidden::make('db_username')
                ->default('root'),
            Hidden::make('db_password')
                ->default('pQLkm8NOkS0gOBox'),
            Hidden::make('db_type')
                ->default('pgsql'),
            Hidden::make('db_port')
                ->default('5432'),
        ];
    }
}
