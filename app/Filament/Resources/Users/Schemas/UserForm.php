<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Personal Information')
                    ->description('Basic user details')
                    ->schema([
                        TextInput::make('name')
                            ->label('Full Name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Enter full name'),

                        TextInput::make('email')
                            ->label('Email Address')
                            ->email()
                            ->required()
                            ->unique(ignorable: fn ($record) => $record)
                            ->maxLength(255)
                            ->placeholder('user@example.com'),

                        FileUpload::make('photo')
                            ->label('Profile Picture')
                            ->image()
                            ->avatar()
                            ->disk('public')
                            ->directory('users/photos')
                            ->maxSize(2048) // 2MB
                            ->circleCropper()
                            ->imageResizeMode('cover')
                            ->imageCropAspectRatio('1:1')
                            ->imageResizeTargetWidth('200')
                            ->imageResizeTargetHeight('200')
                            ->helperText('Upload a profile picture (max 2MB)'),
                    ])
                    ->columns(2),

                Section::make('Contact Information')
                    ->description('Phone numbers and address')
                    ->schema([
                        TextInput::make('phone1')
                            ->label('Primary Phone')
                            ->tel()
                            ->maxLength(20)
                            ->placeholder('+8801XXXXXXXXX')
                            ->helperText('Main contact number'),

                        TextInput::make('phone2')
                            ->label('Secondary Phone')
                            ->tel()
                            ->maxLength(20)
                            ->placeholder('+8801XXXXXXXXX')
                            ->helperText('Optional alternative number'),

                        Textarea::make('address')
                            ->label('Address')
                            ->rows(3)
                            ->maxLength(500)
                            ->placeholder('Enter complete address')
                            ->columnSpanFull(),

                        TextInput::make('reference')
                            ->label('Reference/Referred By')
                            ->maxLength(255)
                            ->placeholder('Reference person name')
                            ->helperText('Who referred this user?'),
                    ])
                    ->columns(2),

                Section::make('Account Settings')
                    ->description('Account access and permissions')
                    ->schema([

                        Select::make('roles')
                            ->label('User Roles')
                            ->relationship('roles', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->options(function () {
                                $user = Auth::user();

                                // যদি super_admin বা admin হয়
                                if ($user->hasAnyRole(['super_admin', 'admin'])) {
                                    return \Spatie\Permission\Models\Role::all()->pluck('name', 'id');
                                }

                                // যদি manager হয়
                                if ($user->hasRole('manager')) {
                                    return \Spatie\Permission\Models\Role::where('name', 'user')->pluck('name', 'id');
                                }

                                // Default (যদি অন্য কেউ হয়)
                                return \Spatie\Permission\Models\Role::where('name', 'user')->pluck('name', 'id');
                            })
                            ->default(function () {
                                $user = Auth::user();

                                // যদি manager হয়, user role ID set করে দিবে
                                if ($user->hasRole('manager')) {
                                    $userRole = \Spatie\Permission\Models\Role::where('name', 'user')->first();

                                    return $userRole ? [$userRole->id] : [];
                                }

                                return [];
                            })
                            ->disabled(function () {
                                // শুধুমাত্র manager এর জন্য disabled
                                return Auth::user()->hasRole('manager');
                            })
                            ->helperText(function () {
                                $user = Auth::user();

                                if ($user->hasAnyRole(['super_admin', 'admin'])) {
                                    return 'Select user roles for permissions';
                                }

                                if ($user->hasRole('manager')) {
                                    return 'Manager can only assign "user" role';
                                }

                                return 'Select user roles';
                            })
                            ->columnSpanFull()
                            ->dehydrated(function () {
                                // manager এর জন্য সবসময় dehydrated (save) হবে
                                return true;
                            }),

                        DateTimePicker::make('email_verified_at')
                            ->label('Email Verified At')
                            ->displayFormat('d M Y, h:i A')
                            ->helperText('Set if email is verified')
                            ->nullable(),
                    ]),

                Section::make('Security')
                    ->description('Password and account security')
                    ->collapsible()
                    ->schema([
                        TextInput::make('password')
                            ->label(fn (string $operation): string => $operation === 'create' ? 'Password' : 'New Password'
                            )
                            ->password()
                            ->required(fn (string $operation): bool => $operation === 'create')
                            ->minLength(8)
                            ->confirmed()
                            ->dehydrated(fn ($state) => filled($state))
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->helperText(fn (string $operation): string => $operation === 'create'
                                    ? 'Create a strong password for the user'
                                    : 'Leave empty to keep current password'
                            )
                            ->revealable()
                            ->columnSpan(1),

                        TextInput::make('password_confirmation')
                            ->label('Confirm Password')
                            ->password()
                            ->required(fn (string $operation): bool => $operation === 'create' || request()->has('change_password')
                            )
                            ->same('password')
                            ->dehydrated(false)
                            ->revealable()
                            ->helperText('Re-enter the password')
                            ->columnSpan(1),
                    ])
                    ->columns(2),
            ]);
    }
}
