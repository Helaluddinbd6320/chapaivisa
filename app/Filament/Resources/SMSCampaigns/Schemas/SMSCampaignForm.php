<?php

namespace App\Filament\Resources\SMSCampaigns\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class SMSCampaignForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('campaign_name')
                    ->default(null),
                Textarea::make('message')
                    ->default(null)
                    ->columnSpanFull(),
                TextInput::make('template_type')
                    ->required()
                    ->default('promotional'),
                Textarea::make('recipient_criteria')
                    ->default(null)
                    ->columnSpanFull(),
                Textarea::make('specific_recipients')
                    ->default(null)
                    ->columnSpanFull(),
                TextInput::make('total_recipients')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('sent_count')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('delivered_count')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('failed_count')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('total_cost')
                    ->required()
                    ->numeric()
                    ->default(0.0)
                    ->prefix('$'),
                TextInput::make('status')
                    ->required()
                    ->default('draft'),
                DateTimePicker::make('scheduled_at'),
                DateTimePicker::make('sent_at'),
                DateTimePicker::make('completed_at'),
                TextInput::make('timezone')
                    ->required()
                    ->default('Asia/Dhaka'),
                Toggle::make('unicode')
                    ->required(),
            ]);
    }
}
