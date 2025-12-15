<?php

namespace App\Filament\Resources\Visas\Tables;

use App\Filament\Resources\Users\Pages\UserProfile;
use App\Filament\Resources\Visas\VisaResource;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use Filament\Actions\ForceDeleteAction;

class VisasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            // ✅ Role-based visibility logic
            ->modifyQueryUsing(function ($query) {
                $user = auth()->user();

                if (! $user->hasAnyRole(['super_admin', 'admin', 'manager'])) {
                    $query->where('user_id', $user->id);
                }

                return $query;
            })

            // ✅ Columns
            ->columns([

                TextColumn::make('created_at')
                    ->label('Created')
                    ->formatStateUsing(function ($state) {
                        $date = \Carbon\Carbon::parse($state);
                        $now = \Carbon\Carbon::now();

                        $diff = $date->diff($now);

                        return "{$diff->y} বছর {$diff->m} মাস {$diff->d} দিন";
                    }),

                TextColumn::make('name')
                    ->searchable()
                    ->url(fn ($record) => VisaResource::getUrl('view', ['record' => $record->id]))
                    ->openUrlInNewTab(false),
                ImageColumn::make('passenger_image')
                    ->disabledClick()
                    ->disk('public')
                    ->url(fn ($record) => $record->passenger_image ? Storage::url($record->passenger_image) : null)
                    ->disabledClick(fn ($record) => ! $record->passenger_image) // ডাটা না থাকলে disable
                    ->openUrlInNewTab(),
                TextColumn::make('passport')->copyable()->searchable()->disabledClick(),
                TextColumn::make('phone_1')->copyable()->searchable()->disabledClick(),
                TextColumn::make('phone_2')->copyable()->searchable()->disabledClick(),
                TextColumn::make('user.name')
                    ->label('Agent')
                    ->searchable()
                    ->url(fn ($record) => $record->user && $record->user_id
                            ? UserProfile::getUrl(['record' => $record->user_id])
                            : '#'
                    )
                    ->color('primary')
                    ->tooltip('View Agent Profile')
                    ->visible(fn () => auth()->user()->hasRole(['super_admin', 'admin', 'manager'])),

                TextColumn::make('medicalCenter.name')->copyable()->searchable()->disabledClick(),
                TextColumn::make('agency.name')->copyable()->disabledClick(),
                TextColumn::make('takamul_category')->disabledClick(),
                TextColumn::make('pc_ref')->disabledClick()->searchable(),

                // Status columns with badges
                TextColumn::make('takamul')
                    ->formatStateUsing(fn ($state) => match (strtolower($state ?? '')) {
                        'yes' => '✅', 'no' => '❌', default => '⏳'
                    })
                    ->badge()
                    ->color(fn ($state) => match (strtolower($state ?? '')) {
                        'yes' => 'success', 'no' => 'danger', default => 'gray'
                    }),
                TextColumn::make('tasheer')
                    ->formatStateUsing(fn ($state) => match (strtolower($state ?? '')) {
                        'yes' => '✅', 'no' => '❌', default => '⏳'
                    })
                    ->badge()
                    ->color(fn ($state) => match (strtolower($state ?? '')) {
                        'yes' => 'success', 'no' => 'danger', default => 'gray'
                    }),
                TextColumn::make('ttc')
                    ->formatStateUsing(fn ($state) => match (strtolower($state ?? '')) {
                        'yes' => '✅', 'no' => '❌', default => '⏳'
                    })
                    ->badge()
                    ->color(fn ($state) => match (strtolower($state ?? '')) {
                        'yes' => 'success', 'no' => 'danger', default => 'gray'
                    }),
                TextColumn::make('embassy')
                    ->formatStateUsing(fn ($state) => match (strtolower($state ?? '')) {
                        'yes' => '✅', 'no' => '❌', default => '⏳'
                    })
                    ->badge()
                    ->color(fn ($state) => match (strtolower($state ?? '')) {
                        'yes' => 'success', 'no' => 'danger', default => 'gray'
                    }),
                TextColumn::make('bmet')
                    ->formatStateUsing(fn ($state) => match (strtolower($state ?? '')) {
                        'yes' => '✅', 'no' => '❌', default => '⏳'
                    })
                    ->badge()
                    ->color(fn ($state) => match (strtolower($state ?? '')) {
                        'yes' => 'success', 'no' => 'danger', default => 'gray'
                    }),

                TextColumn::make('iqama')->copyable()->searchable()->disabledClick(),
                TextColumn::make('visa_type')->copyable()->disabledClick(),
                TextColumn::make('medical_status')->copyable()->disabledClick(),
                TextColumn::make('medical_date')->disabledClick()->date(),
                TextColumn::make('mofa_number')->copyable()->searchable()->disabledClick(),
                TextColumn::make('visa_number')->copyable()->searchable()->disabledClick(),
                TextColumn::make('visa_id_number')->copyable()->searchable()->disabledClick(),
                TextColumn::make('visa_date')->disabledClick()->date(),
                TextColumn::make('visa_condition')->disabledClick()
                                        ->label('Visa Condition')
                            ->formatStateUsing(fn ($state) => match ($state) {
                                'only_visa'        => 'Only Visa',
                                'visa_processing'  => 'Visa + Processing',
                                'only_processing' => 'Only Processing',
                                'full_package'    => 'Full Package',
                                default            => '-',
                            }),

                ImageColumn::make('passport_image')
                    ->disk('public')
                    ->url(fn ($record) => $record->passport_image ? Storage::url($record->passport_image) : null)
                    ->disabledClick(fn ($record) => ! $record->passport_image) // ডাটা না থাকলে disable
                    ->openUrlInNewTab(),
                ImageColumn::make('slip_image')
                    ->disk('public')
                    ->url(fn ($record) => $record->slip_image ? Storage::url($record->slip_image) : null)
                    ->disabledClick(fn ($record) => ! $record->slip_image) // ডাটা না থাকলে disable
                    ->openUrlInNewTab(),
                ImageColumn::make('visa_image')
                    ->disk('public')
                    ->url(fn ($record) => $record->visa_image ? Storage::url($record->visa_image) : null)
                    ->disabledClick(fn ($record) => ! $record->visa_image) // ডাটা না থাকলে disable
                    ->openUrlInNewTab(),

                TextColumn::make('slip_url')
                    ->label('Slip Link')
                    ->getStateUsing(function ($record) {
                        return $record->slip_url ? 'View Slip' : 'No Slip';
                    })
                    ->url(function ($record) {
                        return $record->slip_url ?: null;
                    })
                    ->openUrlInNewTab()
                    ->disabledClick(fn ($record) => ! $record->slip_url)
                    ->color(fn ($record) => $record->slip_url ? 'primary' : 'gray')
                    ->icon(fn ($record) => $record->slip_url ? 'heroicon-o-link' : 'heroicon-o-link-slash')
                    ->iconPosition('after')
                    ->tooltip(function ($record) {
                        if (! $record->slip_url) {
                            return 'No slip link available';
                        }

                        return strlen($record->slip_url) > 50
                            ? substr($record->slip_url, 0, 50).'...'
                            : $record->slip_url;
                    }),
                TextColumn::make('report')->badge()->disabledClick(),
                TextColumn::make('visa_cost')->disabledClick()->money(),
            ])

            // ✅ Filters (act like tabs)
            ->filters([
                TrashedFilter::make(),

                Filter::make('all')->label('All')->query(fn (Builder $query) => $query),
                Filter::make('takamul_no')->label('Takamul No')->query(fn (Builder $query) => $query->where('takamul', 'no')),
                Filter::make('tasheer_no')->label('Tasheer No')->query(fn (Builder $query) => $query->where('tasheer', 'no')),
                Filter::make('ttc_no')->label('TTC No')->query(fn (Builder $query) => $query->where('ttc', 'no')),
                Filter::make('embassy_no')->label('Embassy No')->query(fn (Builder $query) => $query->where('embassy', 'no')),
                Filter::make('bmet_no')->label('BMET No')->query(fn (Builder $query) => $query->where('bmet', 'no')),
            ])

            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                ForceDeleteAction::make()
                ->visible(fn () => auth()->user()?->hasAnyRole(['super_admin', 'admin'])),
                
            ])

            ->toolbarActions([
                BulkActionGroup::make([
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
