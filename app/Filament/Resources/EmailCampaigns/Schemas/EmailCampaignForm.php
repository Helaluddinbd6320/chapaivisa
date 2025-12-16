<?php

namespace App\Filament\Resources\EmailCampaigns\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class EmailCampaignForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Email Campaign')
                    ->tabs([
                        Tab::make('Basic Information')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Section::make('Campaign Details')
                                    ->description('Basic information about your email campaign')
                                    ->collapsible()
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('campaign_name')
                                            ->label('Campaign Name')
                                            ->placeholder('Enter campaign name')
                                            ->required()
                                            ->maxLength(255)
                                            ->columnSpan(1),

                                        TextInput::make('template_type')
                                            ->label('Template Type')
                                            ->placeholder('newsletter, promotion, etc.')
                                            ->required()
                                            ->default('newsletter')
                                            ->columnSpan(1),

                                        TextInput::make('subject')
                                            ->label('Email Subject')
                                            ->placeholder('Enter email subject line')
                                            ->required()
                                            ->maxLength(255)
                                            ->columnSpanFull(),
                                    ]),

                                Section::make('Content')
                                    ->description('The main content of your email')
                                    ->collapsible()
                                    ->schema([
                                        Textarea::make('content')
                                            ->label('Email Content')
                                            ->placeholder('Write your email content here...')
                                            ->required()
                                            ->rows(10)
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        Tab::make('Recipients & Targeting')
                            ->icon('heroicon-o-user-group')
                            ->schema([
                                Section::make('Recipient Settings')
                                    ->description('Define who will receive this campaign')
                                    ->collapsible()
                                    ->columns(2)
                                    ->schema([
                                        TextInput::make('total_recipients')
                                            ->label('Total Recipients')
                                            ->numeric()
                                            ->minValue(0)
                                            ->default(0)
                                            ->disabled()
                                            ->columnSpan(1),

                                        TextInput::make('timezone')
                                            ->label('Campaign Timezone')
                                            ->required()
                                            ->default('Asia/Dhaka')
                                            ->columnSpan(1),
                                    ]),

                                Section::make('Targeting Criteria')
                                    ->description('Filter recipients based on specific criteria')
                                    ->collapsible()
                                    ->schema([
                                        Textarea::make('recipient_criteria')
                                            ->label('Recipient Criteria (JSON/Query)')
                                            ->placeholder('{"status": "active", "country": "Bangladesh"}')
                                            ->rows(3)
                                            ->helperText('Use JSON format for filtering criteria')
                                            ->columnSpanFull(),
                                    ]),

                                Section::make('Specific Recipients')
                                    ->description('Manually add specific email addresses')
                                    ->collapsible()
                                    ->schema([
                                        Textarea::make('specific_recipients')
                                            ->label('Specific Email Addresses')
                                            ->placeholder('email1@example.com, email2@example.com')
                                            ->rows(3)
                                            ->helperText('Enter email addresses separated by commas')
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        Tab::make('Scheduling & Timing')
                            ->icon('heroicon-o-clock')
                            ->schema([
                                Section::make('Campaign Schedule')
                                    ->description('Set when this campaign should be sent')
                                    ->collapsible()
                                    ->columns(2)
                                    ->schema([
                                        DateTimePicker::make('scheduled_at')
                                            ->label('Scheduled Date & Time')
                                            ->placeholder('Select date and time')
                                            ->timezone('Asia/Dhaka')
                                            ->helperText('Bangladesh timezone (UTC+6)')
                                            ->columnSpan(1),

                                        DateTimePicker::make('sent_at')
                                            ->label('Actual Sent Time')
                                            ->placeholder('Will be auto-filled when sent')
                                            ->disabled()
                                            ->columnSpan(1),

                                        DateTimePicker::make('completed_at')
                                            ->label('Completed At')
                                            ->placeholder('Will be auto-filled when completed')
                                            ->disabled()
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        Tab::make('Tracking & Analytics')
                            ->icon('heroicon-o-chart-bar')
                            ->schema([
                                Section::make('Tracking Settings')
                                    ->description('Configure what to track for this campaign')
                                    ->collapsible()
                                    ->columns(2)
                                    ->schema([
                                        Toggle::make('track_opens')
                                            ->label('Track Email Opens')
                                            ->required()
                                            ->default(true)
                                            ->inline(false)
                                            ->columnSpan(1),

                                        Toggle::make('track_clicks')
                                            ->label('Track Link Clicks')
                                            ->required()
                                            ->default(true)
                                            ->inline(false)
                                            ->columnSpan(1),
                                    ]),

                                Section::make('Campaign Statistics')
                                    ->description('Real-time campaign performance metrics')
                                    ->collapsible()
                                    ->columns(3)
                                    ->schema([
                                        TextInput::make('sent_count')
                                            ->label('Sent')
                                            ->numeric()
                                            ->default(0)
                                            ->disabled()
                                            ->columnSpan(1),

                                        TextInput::make('delivered_count')
                                            ->label('Delivered')
                                            ->numeric()
                                            ->default(0)
                                            ->disabled()
                                            ->columnSpan(1),

                                        TextInput::make('opened_count')
                                            ->label('Opened')
                                            ->numeric()
                                            ->default(0)
                                            ->disabled()
                                            ->columnSpan(1),

                                        TextInput::make('clicked_count')
                                            ->label('Clicked')
                                            ->numeric()
                                            ->default(0)
                                            ->disabled()
                                            ->columnSpan(1),

                                        TextInput::make('bounced_count')
                                            ->label('Bounced')
                                            ->numeric()
                                            ->default(0)
                                            ->disabled()
                                            ->columnSpan(1),

                                        TextInput::make('unsubscribed_count')
                                            ->label('Unsubscribed')
                                            ->numeric()
                                            ->default(0)
                                            ->disabled()
                                            ->columnSpan(1),
                                    ]),
                            ]),

                        Tab::make('Advanced Settings')
                            ->icon('heroicon-o-cog')
                            ->schema([
                                Section::make('Campaign Status')
                                    ->description('Manage campaign lifecycle')
                                    ->collapsible()
                                    ->schema([
                                        TextInput::make('status')
                                            ->label('Campaign Status')
                                            ->required()
                                            ->default('draft')
                                            ->placeholder('draft, scheduled, sent, completed')
                                            ->helperText('Current status of the campaign')
                                            ->columnSpanFull(),
                                    ]),

                                Section::make('Attachments')
                                    ->description('Add files to your email campaign')
                                    ->collapsible()
                                    ->schema([
                                        Textarea::make('attachments')
                                            ->label('Attachment URLs')
                                            ->placeholder('https://example.com/file1.pdf, https://example.com/file2.jpg')
                                            ->rows(3)
                                            ->helperText('Enter full URLs separated by commas')
                                            ->columnSpanFull(),
                                    ]),
                            ]),
                    ])
                    ->persistTabInQueryString()
                    ->columnSpanFull(),
            ]);
    }
}
