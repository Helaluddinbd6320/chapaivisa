<?php

namespace App\Filament\Resources\EmailCampaigns\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class EmailCampaignForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('campaign_name')
                    ->default(null),
                TextInput::make('subject')
                    ->default(null),
                Textarea::make('content')
                    ->default(null)
                    ->columnSpanFull(),
                TextInput::make('template_type')
                    ->required()
                    ->default('newsletter'),
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
                TextInput::make('opened_count')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('clicked_count')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('bounced_count')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('unsubscribed_count')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('status')
                    ->required()
                    ->default('draft'),
                DateTimePicker::make('scheduled_at'),
                DateTimePicker::make('sent_at'),
                DateTimePicker::make('completed_at'),
                TextInput::make('timezone')
                    ->required()
                    ->default('Asia/Dhaka'),
                Toggle::make('track_opens')
                    ->required(),
                Toggle::make('track_clicks')
                    ->required(),
                Textarea::make('attachments')
                    ->default(null)
                    ->columnSpanFull(),
            ]);
    }
}
