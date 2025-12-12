<?php

namespace App\Filament\Resources\Accounts\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class AccountForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Section 1: Basic Transaction Info
                Section::make('Transaction Information')
                    ->icon('heroicon-o-document-text')
                    ->description('Core transaction details')
                    ->schema([
                        Select::make('user_id')
                            ->label('Account Holder')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->native(false)
                            ->columnSpan(['md' => 2]),
                        TextInput::make('amount')
                            ->label('Amount')
                            ->numeric()
                            ->required()
                            ->prefix('৳')
                            ->step(0.01)
                            ->minValue(0)
                            ->columnSpan(['md' => 1]),

                        Select::make('transaction_type')
                            ->label('Transaction Type')

                            ->columnSpan(['md' => 2])
                            ->options([
                                'deposit' => '💰 Deposit (জমা)',
                                'withdrawal' => '💸 Withdrawal (উত্তোলন)',
                                'refund' => '↩️ Refund (ফেরত)',
                            ])
                            ->required()
                            ->native(false)
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if (empty($state)) {
                                    return;
                                }
                                $set('transaction_id', strtoupper(Str::random(2)).'-'.date('Ymd').'-'.Str::random(6));
                            }),

                        TextInput::make('transaction_id')
                            ->label('Transaction ID')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50)
                            ->default(fn () => 'TX-'.date('Ymd').'-'.Str::random(6))
                            ->disabled()
                            ->dehydrated()
                            ->prefix('🔢'),

                    ])
                    ->columns(['md' => 2, 'lg' => 3]),

                // Section 2: Payment Details
                Section::make('Payment Details')
                    ->icon('heroicon-o-credit-card')
                    ->description('Payment method and related information')
                    ->schema([
                        Select::make('payment_method')
                            ->label('Payment Method')
                            ->options([
                                'cash' => '💵 Cash (নগদ)',
                                'bank' => '🏦 Bank Transfer (ব্যাংক)',
                                'mobile_banking' => '📱 Mobile Banking (মোবাইল)',
                                'card' => '💳 Card (কার্ড)',
                            ])
                            ->default('cash')
                            ->required()
                            ->native(false)

                            ->columnSpan(['md' => 2])
                            ->live(),

                        // Bank Transfer Details (Conditional)
                        TextInput::make('bank_name')
                            ->label('Bank Name')
                            ->maxLength(255)
                            ->placeholder('e.g., DBBL, Islami Bank')
                            ->visible(fn ($get) => $get('payment_method') === 'bank')
                            ->columnSpan(['md' => 2]),

                        TextInput::make('account_number')
                            ->label('Account Number')
                            ->maxLength(50)
                            ->placeholder('e.g., 1234567890')
                            ->visible(fn ($get) => $get('payment_method') === 'bank')
                            ->columnSpan(['md' => 1]),

                        // Mobile Banking Details (Conditional)
                        TextInput::make('mobile_banking_provider')
                            ->label('Mobile Banking Provider')
                            ->placeholder('bKash, Nagad, Rocket, etc.')
                            ->maxLength(100)
                            ->visible(fn ($get) => $get('payment_method') === 'mobile_banking')
                            ->columnSpan(['md' => 2]),

                        TextInput::make('mobile_number')
                            ->label('Mobile Number')
                            ->tel()
                            ->maxLength(20)
                            ->placeholder('01XXXXXXXXX')
                            ->visible(fn ($get) => $get('payment_method') === 'mobile_banking')
                            ->columnSpan(['md' => 1]),

                        DatePicker::make('payment_date')
                            ->label('Payment Date')
                            ->required()
                            ->default(now())
                            ->displayFormat('d/m/Y')
                            ->columnSpan(['md' => 1]),

                        TextInput::make('receipt_number')
                            ->label('Receipt Number')
                            ->maxLength(100)
                            ->placeholder('e.g., RCPT-20240001')
                            ->columnSpan(['md' => 1]),

                        TextInput::make('reference_number')
                            ->label('Reference Number')
                            ->maxLength(100)
                            ->placeholder('Bank/Mobile Banking Reference')
                            ->columnSpan(['md' => 1]),
                    ])
                    ->columns(['md' => 2, 'lg' => 3]),

                // Section 3: Receipt & Verification
                Section::make('Receipt & Verification')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->description('Receipt and verification status')
                    ->schema([

                        Select::make('status')
                            ->label('Transaction Status')
                            ->options([
                                'pending' => '🟡 Pending',
                                'verified' => '🟢 Verified',
                                'cancelled' => '🔴 Cancelled',
                            ])
                            ->default('pending')
                            ->native(false)
                            ->live()
                            ->columnSpan(['md' => 2]),
                        FileUpload::make('receipt_image')
                            ->label('Receipt Image')
                            ->directory('accounts/receipts')
                            ->image()
                            ->disk('public')
                            ->visibility('public')    // important
                            ->preserveFilenames()     // optional but helpful
                            ->maxSize(1024)
                            ->columnSpan(['md' => 2]),

                        // Toggle::make('is_verified')
                        //     ->label('Verification Status')
                        //     ->required()
                        //     ->inline(false)
                        //     ->onColor('success')
                        //     ->offColor('danger')
                        //     ->columnSpan(['md' => 1]),
                        

                        // Verification Details (Conditional)
                        Select::make('verified_by')
                            ->label('Verified By')
                            ->relationship('verifier', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->native(false)
                            ->default(fn ($operation) => $operation === 'create' ? auth()->id() : null)
                            ->disabled(fn ($context, $record) => $context === 'edit' &&
                                ! in_array(auth()->user()->role, ['Admin', 'super_admin']) &&
                                $record?->verified_by !== null
                            )
                            ->visible(fn ($get) => $get('status') === 'verified')
                            ->columnSpan(['md' => 2]),

                        DateTimePicker::make('verified_at')
                            ->label('Verification Date & Time')
                            ->displayFormat('d/m/Y h:i A')
                            ->visible(fn ($get) => $get('status') === 'verified')
                            ->columnSpan(['md' => 2]),
                    ])
                    ->columns(['md' => 2, 'lg' => 4]),

                // Section 4: Description & Remarks
                Section::make('Description & Remarks')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->description('Additional notes and comments')
                    ->schema([
                        Textarea::make('description')
                            ->label('Transaction Description')
                            ->rows(3)
                            ->maxLength(1000)
                            ->placeholder('Enter detailed description of this transaction...')
                            ->helperText('Describe the purpose and details of this transaction.')
                            ->columnSpanFull(),

                        Textarea::make('remarks')
                            ->label('Additional Remarks')
                            ->rows(2)
                            ->maxLength(500)
                            ->placeholder('Any additional notes or comments...')
                            ->helperText('Internal notes or special instructions.')
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->compact(),
            ]);
    }
}
