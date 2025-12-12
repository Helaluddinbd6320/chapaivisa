<?php

namespace App\Filament\Resources\Agencies\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AgenciesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->disabledClick(),
                TextColumn::make('rl_number')
                    ->searchable()
                    ->disabledClick(),
                TextColumn::make('owner_name')
                    ->disabledClick(),
                TextColumn::make('owner_phone')
                    ->disabledClick(),
                TextColumn::make('manager_name')
                    ->disabledClick(),
                TextColumn::make('manager_phone')
                    ->disabledClick(),
                TextColumn::make('contact_person')
                    ->disabledClick(),
                TextColumn::make('contact_person_phone')
                    ->disabledClick(),
                TextColumn::make('email')
                    ->label('Email address')
                    ->disabledClick(),
                TextColumn::make('website')
                    ->disabledClick(),
                TextColumn::make('city')
                    ->disabledClick(),
                TextColumn::make('state')
                    ->disabledClick(),
                TextColumn::make('zip_code')
                    ->disabledClick(),
                TextColumn::make('country')
                    ->disabledClick(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->disabledClick()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->disabledClick()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
