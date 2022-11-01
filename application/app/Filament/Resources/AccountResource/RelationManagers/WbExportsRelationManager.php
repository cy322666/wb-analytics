<?php

namespace App\Filament\Resources\AccountResource\RelationManagers;

use App\Models\WB\Export;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class WbExportsRelationManager extends RelationManager
{
    protected static string $relationship = 'exports';

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

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('type'),
                Tables\Columns\TextColumn::make('start_at'),
                Tables\Columns\TextColumn::make('finish_at'),
                Tables\Columns\TextColumn::make('status')
                    ->enum([
                        0 => 'Ожидает',
                        1 => 'Выгружается',
                        2 => 'Завершено',
                    ])
            ])
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
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}
