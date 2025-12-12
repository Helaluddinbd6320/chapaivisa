<?php

namespace App\Filament\Resources\Agencies\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class AgencyForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('rl_number')
                    ->required(),
                TextInput::make('owner_name')
                    ->required(),
                TextInput::make('owner_phone')
                    ->tel()
                    ->required(),
                TextInput::make('manager_name')
                    ->default(null),
                TextInput::make('manager_phone')
                    ->tel()
                    ->default(null),
                TextInput::make('contact_person')
                    ->default(null),
                TextInput::make('contact_person_phone')
                    ->tel()
                    ->default(null),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->default(null),
                TextInput::make('website')
                    ->url()
                    ->default(null),
                Textarea::make('address')
                    ->default(null)
                    ->columnSpanFull(),
                TextInput::make('city')
                    ->default(null),
                TextInput::make('state')
                    ->default(null),
                TextInput::make('zip_code')
                    ->default(null),
                TextInput::make('country')
                    ->default('Bangladesh'),
            ]);
    }
}
