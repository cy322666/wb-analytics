<?php

namespace App\Filament\Resources\AccountResource\RelationManagers;

use App\Models\Export;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;

class WbExportsRelationManager extends RelationManager
{
    protected static string $relationship = 'tasks';

    protected static ?string $title = 'Последние события';

    protected static ?string $recordTitleAttribute = 'type';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('type')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    protected function getTablePollingInterval(): ?string
    {
        return '5s';
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id'),
                Tables\Columns\TextColumn::make('command')->label('Команда'),
                Tables\Columns\TextColumn::make('created_at')->label('Создана'),
                Tables\Columns\TextColumn::make('updated_at')->label('Обновлена'),
                Tables\Columns\TextColumn::make('status')->label('Статус')
                    ->enum([
                        0 => 'Ожидает',
                        1 => 'В процессе',
                        2 => 'Завершено',
                        3 => 'Ошибка',
                    ])
            ])
            ->defaultSort('id')
            ->filters([
                //
            ])
            ->headerActions([
//                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
//                Tables\Actions\EditAction::make(),
//                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
//                BulkAction::make('reUpdate')
//                    ->action(function (Collection $records): void {
//                        foreach ($records as $record) {
//                            $record->reUpdate();
//                        }
//                    })
//                    ->deselectRecordsAfterCompletion()
//                    ->requiresConfirmation()
////                    ->action(fn (Export $record) => $record->reUpdate())
//                    ->label('Перезалить'),

                 BulkAction::make('repeat')
                     ->action(function (Collection $records): void {
                         foreach ($records as $record) {
                             $record->reload();
                         }
                     })
                     ->deselectRecordsAfterCompletion()
                     ->requiresConfirmation()
                     ->label('Повторить')
            ])->poll('5s');
    }
}
