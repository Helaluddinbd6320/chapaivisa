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
            ->modifyQueryUsing(function ($query) {
                $user = auth()->user();
                if (!$user?->hasAnyRole(['super_admin', 'admin', 'manager'])) {
                    $query->where('user_id', $user->id);
                }
                return $query->visaCostZeroFirst()->latestUpdated();
            })
            ->columns([
                TextColumn::make('name')->searchable(),
                TextColumn::make('visa_cost')->money()->sortable(),
                TextColumn::make('updated_at')->dateTime('d M, Y H:i')->sortable(),
                TextColumn::make('user.name')
                    ->label('Agent')
                    ->url(fn($record)=>$record->user?->id?UserProfile::getUrl(['record'=>$record->user->id]):'#')
                    ->color('primary')
                    ->visible(fn()=>auth()->user()?->hasRole(['super_admin','admin','manager'])),
                ImageColumn::make('passenger_image')
                    ->disk('public')
                    ->url(fn($record)=>$record->passenger_image?Storage::url($record->passenger_image):null)
                    ->openUrlInNewTab()
                    ->disabledClick(fn($record)=>!$record->passenger_image),
            ])
            ->filters([
                TrashedFilter::make(),
                Filter::make('recent')->query(fn(Builder $query)=>$query->where('created_at','>=',now()->subDays(7))),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                ForceDeleteAction::make()->visible(fn()=>auth()->user()?->hasAnyRole(['super_admin','admin'])),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
