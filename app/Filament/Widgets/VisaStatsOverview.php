<?php

namespace App\Filament\Widgets;

use App\Models\Visa;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class VisaStatsOverview extends BaseWidget
{
    protected static ?int $sort = 1; // প্রথমে দেখাবে

    protected function getStats(): array
    {
        $user = Auth::user();
        $query = Visa::query();

        // Role-based query modification
        if (! $user->hasAnyRole(['super_admin', 'admin', 'manager'])) {
            $query->where('user_id', $user->id);
        }

        $totalVisas = $query->count();
        $takamulNo = (clone $query)->where('takamul', 'no')->count();
        $tasheerNo = (clone $query)->where('tasheer', 'no')->count();
        $ttcNo = (clone $query)->where('ttc', 'no')->count();
        $embassyNo = (clone $query)->where('embassy', 'no')->count();
        $bmetNo = (clone $query)->where('bmet', 'no')->count();

        // Report Status Statistics
        $pendingReports = (clone $query)->where('report', 'pending')->count();
        $approvedReports = (clone $query)->where('report', 'approved')->count();
        $completedReports = (clone $query)->where('report', 'completed')->count();

        // Base URL for visas resource
        $baseUrl = route('filament.admin.resources.visas.index');

        return [
            // Total Visas
            Stat::make('Total Visas', $totalVisas)
                ->description('All visa records')
                ->descriptionIcon('heroicon-o-document-text')
                ->color('primary')
                ->chart([7, 2, 10, 3, 15, 4, 17])
                ->url($baseUrl.'?tab=all')
                ->openUrlInNewTab(false)
                ->extraAttributes([
                    'class' => 'cursor-pointer hover:bg-gray-50 transition-colors',
                ]),

            // Takamul Status - শুধুমাত্র No
            Stat::make('Takamul No', $takamulNo)
                ->description('Remaining to complete')
                ->descriptionIcon($takamulNo > 0 ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                ->color($takamulNo > 0 ? 'danger' : 'success')
                ->chart([$takamulNo])
                ->url($takamulNo > 0 ? $baseUrl.'?tab=takamul_no' : null)
                ->openUrlInNewTab(false)
                ->extraAttributes([
                    'class' => $takamulNo > 0 ? 'cursor-pointer hover:bg-gray-50 transition-colors' : 'cursor-default',
                ]),

            // Tasheer Status - শুধুমাত্র No
            Stat::make('Tasheer No', $tasheerNo)
                ->description('Remaining to complete')
                ->descriptionIcon($tasheerNo > 0 ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                ->color($tasheerNo > 0 ? 'danger' : 'success')
                ->chart([$tasheerNo])
                ->url($tasheerNo > 0 ? $baseUrl.'?tab=tasheer_no' : null)
                ->openUrlInNewTab(false)
                ->extraAttributes([
                    'class' => $tasheerNo > 0 ? 'cursor-pointer hover:bg-gray-50 transition-colors' : 'cursor-default',
                ]),

            // TTC Status - শুধুমাত্র No
            Stat::make('TTC No', $ttcNo)
                ->description('Remaining to complete')
                ->descriptionIcon($ttcNo > 0 ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                ->color($ttcNo > 0 ? 'danger' : 'success')
                ->chart([$ttcNo])
                ->url($ttcNo > 0 ? $baseUrl.'?tab=ttc_no' : null)
                ->openUrlInNewTab(false)
                ->extraAttributes([
                    'class' => $ttcNo > 0 ? 'cursor-pointer hover:bg-gray-50 transition-colors' : 'cursor-default',
                ]),

            // Embassy Status - শুধুমাত্র No
            Stat::make('Embassy No', $embassyNo)
                ->description('Remaining to complete')
                ->descriptionIcon($embassyNo > 0 ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                ->color($embassyNo > 0 ? 'danger' : 'success')
                ->chart([$embassyNo])
                ->url($embassyNo > 0 ? $baseUrl.'?tab=embassy_no' : null)
                ->openUrlInNewTab(false)
                ->extraAttributes([
                    'class' => $embassyNo > 0 ? 'cursor-pointer hover:bg-gray-50 transition-colors' : 'cursor-default',
                ]),

            // BMET Status - শুধুমাত্র No
            Stat::make('BMET No', $bmetNo)
                ->description('Remaining to complete')
                ->descriptionIcon($bmetNo > 0 ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                ->color($bmetNo > 0 ? 'danger' : 'success')
                ->chart([$bmetNo])
                ->url($bmetNo > 0 ? $baseUrl.'?tab=bmet_no' : null)
                ->openUrlInNewTab(false)
                ->extraAttributes([
                    'class' => $bmetNo > 0 ? 'cursor-pointer hover:bg-gray-50 transition-colors' : 'cursor-default',
                ]),

            // Report Pending
            Stat::make('Pending Reports', $pendingReports)
                ->description('Click to view')
                ->descriptionIcon($pendingReports > 0 ? 'heroicon-o-clock' : 'heroicon-o-check-circle')
                ->color($pendingReports > 0 ? 'warning' : 'success')
                ->chart([$pendingReports])
                ->url($pendingReports > 0 ? $baseUrl.'?tab=report_pending' : null)
                ->openUrlInNewTab(false)
                ->extraAttributes([
                    'class' => $pendingReports > 0 ? 'cursor-pointer hover:bg-gray-50 transition-colors' : 'cursor-default',
                ]),

            // Report Approved
            Stat::make('Approved Reports', $approvedReports)
                ->description('Click to view')
                ->descriptionIcon($approvedReports > 0 ? 'heroicon-o-hand-thumb-up' : 'heroicon-o-flag')
                ->color($approvedReports > 0 ? 'info' : 'gray')
                ->chart([$approvedReports])
                ->url($approvedReports > 0 ? $baseUrl.'?tab=report_approved' : null)
                ->openUrlInNewTab(false)
                ->extraAttributes([
                    'class' => $approvedReports > 0 ? 'cursor-pointer hover:bg-gray-50 transition-colors' : 'cursor-default',
                ]),

            // Report Completed
            Stat::make('Completed Reports', $completedReports)
                ->description('Click to view')
                ->descriptionIcon($completedReports > 0 ? 'heroicon-o-check-circle' : 'heroicon-o-flag')
                ->color($completedReports > 0 ? 'success' : 'gray')
                ->chart([$completedReports])
                ->url($completedReports > 0 ? $baseUrl.'?tab=report_completed' : null)
                ->openUrlInNewTab(false)
                ->extraAttributes([
                    'class' => $completedReports > 0 ? 'cursor-pointer hover:bg-gray-50 transition-colors' : 'cursor-default',
                ]),
        ];
    }

    protected function getColumns(): int
    {
        return 3;
    }
}
