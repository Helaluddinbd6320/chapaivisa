<?php

namespace App\Filament\Resources\Visas\Tables;

use App\Filament\Resources\Users\Pages\UserProfile;
use App\Filament\Resources\Visas\VisaResource;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
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

class VisasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            // ✅ Table Sorting: ০ cost উপরে, তারপর updated_at DESC
            ->defaultSort('updated_at', 'desc')
            ->query(fn (Builder $query) => $query
                ->orderByRaw('CASE WHEN visa_cost = 0 THEN 0 ELSE 1 END') // 0 cost উপরে
                ->orderByDesc('updated_at') // লেটেস্ট আপডেট উপরে
                ->orderByDesc('id') // fallback
            )

            // ✅ Role-based visibility
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
                    ->sortable()
                    ->formatStateUsing(fn ($state) => \Carbon\Carbon::parse($state)->diffForHumans())
                    ->description(fn ($record) => $record->created_at->format('d M, Y h:i A'))
                    ->weight('bold'),

                TextColumn::make('name')
                    ->searchable()
                    ->url(fn ($record) => VisaResource::getUrl('view', ['record' => $record->id]))
                    ->openUrlInNewTab(false),

                ImageColumn::make('passenger_image')
                    ->disabledClick()
                    ->disk('public')
                    ->url(fn ($record) => $record->passenger_image ? Storage::url($record->passenger_image) : null)
                    ->disabledClick(fn ($record) => ! $record->passenger_image)
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

                // Status Columns
                TextColumn::make('takamul')
                    ->label('Takamul')
                    ->formatStateUsing(fn ($state) => match (strtolower($state ?? '')) {
                        'yes' => '✅ Completed',
                        'no' => '❌ Not Completed',
                        'na' => '➖ Not Required',
                        default => '⏳',
                    })
                    ->badge()
                    ->color(fn ($state) => match (strtolower($state ?? '')) {
                        'yes' => 'success',
                        'no' => 'danger',
                        'na' => 'gray',
                        default => 'secondary',
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
                TextColumn::make('visa_condition')
                    ->label('Visa Condition')
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'only_visa' => 'Only Visa',
                        'visa_processing' => 'Visa + Processing',
                        'only_processing' => 'Only Processing',
                        'full_package' => 'Full Package',
                        default => '-',
                    }),

                ImageColumn::make('passport_image')
                    ->disk('public')
                    ->url(fn ($record) => $record->passport_image ? Storage::url($record->passport_image) : null)
                    ->disabledClick(fn ($record) => ! $record->passport_image)
                    ->openUrlInNewTab(),

                ImageColumn::make('slip_image')
                    ->disk('public')
                    ->url(fn ($record) => $record->slip_image ? Storage::url($record->slip_image) : null)
                    ->disabledClick(fn ($record) => ! $record->slip_image)
                    ->openUrlInNewTab(),

                ImageColumn::make('visa_image')
                    ->disk('public')
                    ->url(fn ($record) => $record->visa_image ? Storage::url($record->visa_image) : null)
                    ->disabledClick(fn ($record) => ! $record->visa_image)
                    ->openUrlInNewTab(),

                TextColumn::make('slip_url')
                    ->label('Slip Link')
                    ->getStateUsing(fn ($record) => $record->slip_url ? 'View Slip' : 'No Slip')
                    ->url(fn ($record) => $record->slip_url ?: null)
                    ->openUrlInNewTab()
                    ->disabledClick(fn ($record) => ! $record->slip_url)
                    ->color(fn ($record) => $record->slip_url ? 'primary' : 'gray')
                    ->icon(fn ($record) => $record->slip_url ? 'heroicon-o-link' : 'heroicon-o-link-slash')
                    ->iconPosition('after')
                    ->tooltip(fn ($record) => $record->slip_url ? substr($record->slip_url, 0, 50).'...' : 'No slip link available'),

                TextColumn::make('report')->badge()->disabledClick(),
                TextColumn::make('visa_cost')->disabledClick()->money(),
            ])

            // ✅ Filters
            ->filters([
                TrashedFilter::make(),

                Filter::make('all')->label('All')->query(fn (Builder $query) => $query),
                Filter::make('takamul_no')->label('Takamul No')->query(fn (Builder $query) => $query->where('takamul', 'no')),
                Filter::make('tasheer_no')->label('Tasheer No')->query(fn (Builder $query) => $query->where('tasheer', 'no')),
                Filter::make('ttc_no')->label('TTC No')->query(fn (Builder $query) => $query->where('ttc', 'no')),
                Filter::make('embassy_no')->label('Embassy No')->query(fn (Builder $query) => $query->where('embassy', 'no')),
                Filter::make('bmet_no')->label('BMET No')->query(fn (Builder $query) => $query->where('bmet', 'no')),

                Filter::make('recent')
                    ->label('Recent (Last 7 Days)')
                    ->query(fn (Builder $query) => $query->where('created_at', '>=', now()->subDays(7))),
            ])

            // ✅ Record Actions
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                ForceDeleteAction::make()
                    ->visible(fn () => auth()->user()?->hasAnyRole(['super_admin', 'admin'])),
            ])

            // ✅ Toolbar Actions
            ->toolbarActions([
                BulkActionGroup::make([
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
