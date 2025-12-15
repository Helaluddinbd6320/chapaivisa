<?php

namespace App\Filament\Resources\Visas\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class VisaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Section 1: Personal Information
                Section::make('Personal Information')
                    ->icon('heroicon-o-user')
                    ->description('Applicant personal details')
                    ->schema([
                        TextInput::make('name')
                            ->label('Full Name')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),

                        TextInput::make('passport')
                            ->label('Passport Number')
                            ->required()
                            ->maxLength(50)
                            ->unique(ignoreRecord: true),

                        TextInput::make('phone_1')
                            ->label('Primary Phone')
                            ->tel()
                            ->required()
                            ->maxLength(20)
                            ->regex('/^(\+?88)?\s?-?0?1[3-9]\d{2}-?\d{6}$/'), 

                        TextInput::make('phone_2')
                            ->label('Secondary Phone')
                            ->tel()
                            ->maxLength(20),

                        Select::make('user_id')
                            ->label('Agent/User')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                    ])
                    ->columns(2),

                // Section 2: Agency & Medical Details
                Section::make('Agency & Medical Information')
                    ->icon('heroicon-o-building-office')
                    ->description('Agency and medical details')
                    ->schema([
                        Select::make('agency_id')
                            ->label('Agency')
                            ->relationship('agency', 'name')
                            ->searchable()
                            ->preload(),

                        Select::make('medical_center_id')
                            ->label('Medical Center')
                            ->relationship('medicalCenter', 'name')
                            ->searchable()
                            ->preload(),

                        TextInput::make('medical_status')
                            ->label('Medical Status')
                            ->maxLength(100),

                        DatePicker::make('medical_date')
                            ->label('Medical Date')
                            ->displayFormat('d/m/Y'),
                        TextInput::make('slip_url')
                            ->label('Slip URL (if online)')
                            ->url()
                            ->maxLength(500)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                // Section 3: Visa Details
                Section::make('Visa Information')
                    ->icon('heroicon-o-document-text')
                    ->description('Visa processing details')
                    ->schema([
                        TextInput::make('visa_number')
                            ->label('Visa Number')
                            ->maxLength(100)
                            ->unique(
                                table: 'visas',
                                column: 'visa_number',
                                ignorable: fn ($record) => $record,
                            ),

                        TextInput::make('visa_id_number')
                            ->label('Visa ID Number')
                            ->maxLength(100),

                    

                        Select::make('visa_type')
                            ->label('Visa Type')
                            ->options([
                                '03_months' => '03 Months',
                                '15_months' => '15 Months',
                            ])
                            ->default('03_months')
                            ->native(false)
                            ->required(false),

                        DatePicker::make('visa_date')
                            ->label('Visa Date')
                            ->displayFormat('d/m/Y'),

                        

                        Select::make('visa_condition')
                            ->label('Visa Condition')
                            ->options([
                                'only_visa'        => 'Only Visa',
                                'visa_processing'  => 'Visa + Processing',
                                'only_processing' => 'Only Processing',
                                'full_package'    => 'Full Package',
                            ])
                            ->default('only_visa')
                            ->native(false)
                            ->columnSpan(2)
                            ->required(false),

                        TextInput::make('mofa_number')
                            ->label('MOFA Number')
                            ->columnSpan(1)
                            ->maxLength(100),
                        TextInput::make('iqama')
                            ->label('Iqama Number')
                            ->maxLength(100)
                            ->columnSpan(1),

                    ])
                    ->columns(3),

                // Section 4: Processing Status
                Section::make('Processing Status')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->description('Application processing status')
                    ->schema([
                        Select::make('report')
                            ->label('Current Status')
                            ->options([
                                'pending' => 'Pending',
                                'approved' => 'Approved',
                                'completed' => 'Completed',
                            ])
                            ->default('pending')
                            ->required()
                            ->columnSpan(1),

                        TextInput::make('visa_cost')
                            ->label('Visa Cost')
                            ->required()
                            ->numeric()
                            ->prefix('$')
                            ->default(0.00)
                            ->columnSpan(1),

                        Select::make('takamul')
                            ->label('Takamul')
                            ->options(['yes' => '✅ Yes', 'no' => '❌ No', 'na'  => '➖ Not Required'])
                            ->default('na')
                            ->columnSpan(1),

                        Select::make('takamul_category')
                            ->label('Takamul Category')
                            ->options([
                                'Warehouse Worker' => 'Warehouse Worker',
                                'Construction formwork Carpenter' => 'Construction formwork Carpenter',
                                'Heating Ventilation and Air Conditioning' => 'Heating Ventilation and Air Conditioning',
                                'Auto Electrician' => 'Auto Electrician',
                                'HVAC Mechanic' => 'HVAC Mechanic',
                                'Sellers' => 'Sellers',
                                'Offices and Facilities Cleaning Worker' => 'Offices and Facilities Cleaning Worker',
                                'Barista' => 'Barista',
                                'Transport Car Worker' => 'Transport Car Worker',
                                'Electrician' => 'Electrician',
                                'Construction and Building' => 'Construction and Building',
                                'Blacksmithing' => 'Blacksmithing',
                                'garden clean worker' => 'garden clean worker',
                                'Drilling and excavating' => 'Drilling and excavating',
                                'Kitchen Worker' => 'Kitchen Worker',
                                'Pipe Installer' => 'Pipe Installer',
                                'Gas Station Attendant' => 'Gas Station Attendant',
                                'Street clean worker' => 'Street clean worker',
                                'Car body Repair' => 'Car body Repair',
                                'Building Electrician' => 'Building Electrician',
                                'Barber' => 'Barber',
                                'Bus Driver' => 'Bus Driver',
                                'Tailoring' => 'Tailoring',
                                'Motorcycle Driver' => 'Motorcycle Driver',
                                'Taxi Driver' => 'Taxi Driver',
                                'Construction Worker' => 'Construction Worker',
                                'Hospital Cleaner' => 'Hospital Cleaner',
                                'Car Driver' => 'Car Driver',
                                'Chef' => 'Chef',
                                'Hospitality and Food Services' => 'Hospitality and Food Services',
                                'Heavy Truck Driver' => 'Heavy Truck Driver',
                                'Facade and Roof Cleaning' => 'Facade and Roof Cleaning',
                                'Hairdresser' => 'Hairdresser',
                                'Nail Care Specialist' => 'Nail Care Specialist',
                                'Constructing Worker' => 'Constructing Worker',
                                'Laundry and Ironing' => 'Laundry and Ironing',
                                'System Category' => 'System Category',
                                'Load and unload worker' => 'Load and unload worker',
                                'Fast Food Preparation' => 'Fast Food Preparation',
                                'Plumber' => 'Plumber',
                                'Tile setter' => 'Tile setter',
                                'Builder' => 'Builder',
                                'Blacksmith' => 'Blacksmith',
                                'Electrical Devices Maintenance Technician' => 'Electrical Devices Maintenance Technician',
                                'Road Maintenance Worker' => 'Road Maintenance Worker',
                                'Workshop Worker' => 'Workshop Worker',
                                'Manufacturing Officer' => 'Manufacturing Officer',
                                'Unique Occupation Worker' => 'Unique Occupation Worker',
                                'Plasterer' => 'Plasterer',
                                'Painting' => 'Painting',
                                'Tilling' => 'Tilling',
                                'Blacksmith construction' => 'Blacksmith construction',
                                'Plumbing' => 'Plumbing',
                                'Automotive Mechanics' => 'Automotive Mechanics',
                                'Welding' => 'Welding',
                                'Electronic Telecom' => 'Electronic Telecom',
                                'Carpentry' => 'Carpentry',
                                'Manufacture and processing of metals' => 'Manufacture and processing of metals',
                                'Machine repair' => 'Machine repair',
                                'Automotive Primary Service' => 'Automotive Primary Service',
                                'Mining' => 'Mining',
                                'Stone Crushers' => 'Stone Crushers',
                                'Mechanical' => 'Mechanical',
                                'Power Cable Connector' => 'Power Cable Connector',
                                'Auto Mechanic' => 'Auto Mechanic',
                                'Auto plumber' => 'Auto plumber',
                                'Heavy Equipment Technician' => 'Heavy Equipment Technician',
                                'Electrical Mechanic' => 'Electrical Mechanic',
                                'Furniture Carpenter' => 'Furniture Carpenter',
                                'Stone mason' => 'Stone mason',
                                'Scaffold Laborer' => 'Scaffold Laborer',
                                'Minitruck Driver' => 'Minitruck Driver',
                                'Truck Driver' => 'Truck Driver',
                                'Baker' => 'Baker',
                                'Butchering' => 'Butchering',
                                'Furniture Assembling Worker' => 'Furniture Assembling Worker',
                                'Gypsum Worker' => 'Gypsum Worker',
                                'Pipe and Boiler Insulation Worker' => 'Pipe and Boiler Insulation Worker',
                                'Power Lines Operator' => 'Power Lines Operator',
                                'Trailer Truck Driver' => 'Trailer Truck Driver',
                                'Concrete' => 'Concrete',
                                'Packaging the Shelves Worker' => 'Packaging the Shelves Worker',
                                'Packaging Worker' => 'Packaging Worker',
                            ])
                            ->default('Load and unload worker')
                            ->columnSpan(1),

                        Select::make('tasheer')
                            ->label('Tasheer')
                            ->options(['yes' => '✅ Yes', 'no' => '❌ No'])
                            ->default('no')
                            ->columnSpan(1),

                        Select::make('ttc')
                            ->label('TTC')
                            ->options(['yes' => '✅ Yes', 'no' => '❌ No'])
                            ->default('no')
                            ->columnSpan(1),
                        TextInput::make('pc_ref')
                            ->label('PC Reference')
                            ->maxLength(100),
                        Select::make('bmet')
                            ->label('BMET')
                            ->options(['yes' => '✅ Yes', 'no' => '❌ No'])
                            ->default('no')
                            ->columnSpan(1),

                        Select::make('embassy')
                            ->label('Embassy')
                            ->options(['yes' => '✅ Yes', 'no' => '❌ No'])
                            ->default('no')
                            ->columnSpan(1),
                    ])
                    ->columns(3),

                // Section 5: Document Uploads
                Section::make('Document Uploads')
                    ->icon('heroicon-o-photo')
                    ->description('Upload relevant documents')
                    ->schema([
                        FileUpload::make('passenger_image')
                            ->label('Passenger Photo')
                            ->image()
                            ->disk('public')
                            ->directory('visas/passenger')
                            ->avatar()
                            ->visibility('public') 
                            ->maxSize(1024),

                        FileUpload::make('passport_image')
                            ->label('Passport Copy')
                            ->image()
                            ->disk('public')
                            ->directory('visas/passport')
                            ->maxSize(1024),

                        FileUpload::make('slip_image')
                            ->label('Payment Slip')
                            ->image()
                            ->directory('visas/slips')
                            ->maxSize(1024),

                        FileUpload::make('visa_image')
                            ->label('Visa Copy')
                            ->image()
                            ->directory('visas/visa')
                            ->maxSize(1024),

                    ])
                    ->columns(2),
            ]);
    }
}
