<?php

namespace App\Filament\Resources\MedicalCenters\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class MedicalCentersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->disabledClick(),
                TextColumn::make('country')
                    ->disabledClick(),
                TextColumn::make('city')
                    ->searchable()
                    ->disabledClick(),
                TextColumn::make('phone')
                    ->disabledClick(),
                TextColumn::make('email')
                    ->label('Email address')
                    ->disabledClick(),
                TextColumn::make('contact_person')
                    ->disabledClick(),
                TextColumn::make('status')
                    ->badge()
                    ->disabledClick(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->disabledClick()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->disabledClick()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->disabledClick()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
