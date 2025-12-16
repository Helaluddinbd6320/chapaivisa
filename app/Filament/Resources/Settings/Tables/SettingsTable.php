<?php

namespace App\Filament\Resources\Settings\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SettingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('app_name')
                    ->searchable(),
                TextColumn::make('logo')
                    ->searchable(),
                TextColumn::make('favicon')
                    ->searchable(),
                TextColumn::make('office_phone')
                    ->searchable(),
                TextColumn::make('office_phone2')
                    ->searchable(),
                TextColumn::make('office_email')
                    ->searchable(),
                TextColumn::make('whatsapp_number')
                    ->searchable(),
                TextColumn::make('facebook_url')
                    ->searchable(),
                TextColumn::make('instagram_url')
                    ->searchable(),
                TextColumn::make('primary_color')
                    ->searchable(),
                TextColumn::make('secondary_color')
                    ->searchable(),
                TextColumn::make('tertiary_color')
                    ->searchable(),
                TextColumn::make('currency')
                    ->searchable(),
                TextColumn::make('currency_symbol')
                    ->searchable(),
                TextColumn::make('timezone')
                    ->searchable(),
                TextColumn::make('date_format')
                    ->searchable(),
                IconColumn::make('maintenance_mode')
                    ->boolean(),
                TextColumn::make('smtp_host')
                    ->searchable(),
                TextColumn::make('smtp_port')
                    ->searchable(),
                TextColumn::make('smtp_username')
                    ->searchable(),
                TextColumn::make('smtp_encryption')
                    ->searchable(),
                TextColumn::make('smtp_from_address')
                    ->searchable(),
                TextColumn::make('smtp_from_name')
                    ->searchable(),
                IconColumn::make('enable_email_marketing')
                    ->boolean(),
                TextColumn::make('email_marketing_schedule_time')
                    ->time()
                    ->sortable(),
                TextColumn::make('email_daily_limit')
                    ->numeric()
                    ->sortable(),
                IconColumn::make('email_tracking')
                    ->boolean(),
                IconColumn::make('enable_sms_marketing')
                    ->boolean(),
                TextColumn::make('sms_provider')
                    ->searchable(),
                TextColumn::make('sms_api_key')
                    ->searchable(),
                TextColumn::make('sms_api_secret')
                    ->searchable(),
                TextColumn::make('sms_sender_id')
                    ->searchable(),
                TextColumn::make('sms_api_url')
                    ->searchable(),
                TextColumn::make('sms_daily_limit')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('sms_unit_price')
                    ->money()
                    ->sortable(),
                TextColumn::make('invoice_prefix')
                    ->searchable(),
                TextColumn::make('google_analytics_id')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
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
