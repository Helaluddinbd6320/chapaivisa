<?php

namespace App\Filament\Resources\Visas\Pages;

use App\Filament\Resources\Visas\VisaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab; // v5 এ কাজ করে, v4 এ এখানে কেবল illustration
use Illuminate\Database\Eloquent\Builder;

class ListVisas extends ListRecords
{
    protected static string $resource = VisaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    /**
     * Tabs logic (v5 style example)
     * Note: In v4, native Tabs class not available. Use getTableQuery() + Blade header for real implementation.
     */
    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All')
                ->modifyQueryUsing(fn (Builder $query) => $query),

            'takamul_no' => Tab::make('Takamul No')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('takamul', 'no')),

            'tasheer_no' => Tab::make('Tasheer No')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('tasheer', 'no')),

            'ttc_no' => Tab::make('TTC No')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('ttc', 'no')),

            'embassy_no' => Tab::make('Embassy No')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('embassy', 'no')),

            'bmet_no' => Tab::make('BMET No')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('bmet', 'no')),
            
            // Report Status Tabs
            'report_pending' => Tab::make('Report Pending')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('report', 'pending')),
                
            'report_approved' => Tab::make('Report Approved')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('report', 'approved')),
                
            'report_completed' => Tab::make('Report Completed')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('report', 'completed')),
        ];
    }

    /**
     * Filter table query based on 'tab' query param (v4 compatible)
     */
    protected function getTableQuery(): Builder
    {
        $query = parent::getTableQuery();
        $tab = request()->get('tab', 'all');

        return match ($tab) {
            'takamul_no' => $query->where('takamul', 'no'),
            'tasheer_no' => $query->where('tasheer', 'no'),
            'ttc_no' => $query->where('ttc', 'no'),
            'embassy_no' => $query->where('embassy', 'no'),
            'bmet_no' => $query->where('bmet', 'no'),
            'report_pending' => $query->where('report', 'pending'),
            'report_approved' => $query->where('report', 'approved'),
            'report_completed' => $query->where('report', 'completed'),
            default => $query,
        };
    }
    
    /**
     * Get the active tab based on query parameter
     */
    public function getActiveTab(): string
    {
        return request()->get('tab', 'all');
    }
}