<?php

namespace App\Providers\Filament;

use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->brandName('Visa Office Chapai International')
            ->favicon(asset('favicon.png')) // এখানে favicon যোগ করুন
            ->colors([
                'primary' => Color::Amber,
            ])
            ->renderHook(
                'panels::user-menu.before',
                function () {

                    if (! Auth::check()) {
                        return '';
                    }

                    $user = Auth::user();
                    $isAdmin = $user->hasAnyRole(['super_admin', 'admin', 'manager']);

                    $data = $this->getCachedAmounts($user, $isAdmin);

                    $balance = $data['balance'];
                    $deposits = $data['deposits'];
                    $visaCosts = $data['visa'];
                    $withdrawals = $data['withdrawals'];
                    $refunds = $data['refunds'];

                    // No decimals
                    $formattedBalance = number_format($balance, 0);
                    $formattedDeposit = number_format($deposits, 0);
                    $formattedVisa = number_format($visaCosts, 0);
                    $formattedWithdrawal = number_format($withdrawals, 0);
                    $formattedRefund = number_format($refunds, 0);

                    $label = $isAdmin ? 'System Balance' : 'My Balance';

                    return <<<HTML
<div class="flex items-center mr-4">
    <button type="button"
        class="flex flex-wrap items-center gap-2 px-4 py-1.5 rounded-full
               bg-linear-to-r from-amber-500 via-orange-500 to-pink-500
               border border-white/20
               shadow-md shadow-amber-500/30
               hover:shadow-lg hover:shadow-pink-500/40
               text-[13px] font-semibold text-white
               transition-all duration-200 whitespace-nowrap">

        <span class="text-white/90">
            {$label}:
        </span>

        <span class="font-bold">৳{$formattedBalance}</span>

        <span class="text-white/80">Deposits:</span>
        <span class="font-semibold">৳{$formattedDeposit}</span>

        <span class="text-white/80">Visa:</span>
        <span class="font-semibold">-৳{$formattedVisa}</span>

        <span class="text-white/80">W/D:</span>
        <span class="font-semibold">-৳{$formattedWithdrawal}</span>

        <span class="text-white/80">Refund:</span>
        <span class="font-semibold">-৳{$formattedRefund}</span>

    </button>
</div>
HTML;
                }
            )

            ->navigationItems([
                NavigationItem::make('balance')
                    ->group('Account')
                    ->label(fn () => $this->getBalanceLabel())
                    ->icon(function () {
                        $user = Auth::user();

                        return $user && $user->hasAnyRole(['super_admin', 'admin', 'manager'])
                            ? 'heroicon-o-chart-bar'
                            : 'heroicon-o-currency-dollar';
                    })
                    ->sort(100)
                    ->badge(fn () => $this->getFormattedBalance())
                    ->visible(fn () => $this->shouldShowBalance()),
            ])

            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                \App\Filament\Widgets\VisaStatsOverview::class,
                \App\Filament\Widgets\TopNegativeBalanceWidget::class,
            ])

            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])

            ->plugin(
                FilamentShieldPlugin::make()
                    ->gridColumns([
                        'default' => 1,
                        'sm' => 2,
                        'lg' => 3,
                    ])
                    ->sectionColumnSpan(1)
                    ->checkboxListColumns([
                        'default' => 1,
                        'sm' => 2,
                        'lg' => 3,
                    ])
                    ->resourceCheckboxListColumns([
                        'default' => 1,
                        'sm' => 2,
                    ])
            )

            ->authMiddleware([
                Authenticate::class,
            ]);
    }

    /* =========================
        Cached Helper Methods
    ========================== */

    private function getCachedAmounts($user, $isAdmin): array
    {
        static $cache = [];

        $key = $isAdmin ? 'admin' : $user->id;

        if (isset($cache[$key])) {
            return $cache[$key];
        }

        if ($isAdmin) {
            $deposits = DB::table('accounts')
                ->where('transaction_type', 'deposit')
                ->sum('amount');

            $visa = DB::table('visas')->sum('visa_cost');

            $withdrawals = DB::table('accounts')
                ->where('transaction_type', 'withdrawal')
                ->sum('amount');

            $refunds = DB::table('accounts')
                ->where('transaction_type', 'refund')
                ->sum('amount');
        } else {
            $deposits = DB::table('accounts')
                ->where('user_id', $user->id)
                ->where('transaction_type', 'deposit')
                ->sum('amount');

            $visa = DB::table('visas')
                ->where('user_id', $user->id)
                ->sum('visa_cost');

            $withdrawals = DB::table('accounts')
                ->where('user_id', $user->id)
                ->where('transaction_type', 'withdrawal')
                ->sum('amount');

            $refunds = DB::table('accounts')
                ->where('user_id', $user->id)
                ->where('transaction_type', 'refund')
                ->sum('amount');
        }

        $balance = $deposits - $visa - $withdrawals - $refunds;

        return $cache[$key] = [
            'deposits' => $deposits,
            'visa' => $visa,
            'withdrawals' => $withdrawals,
            'refunds' => $refunds,
            'balance' => $balance,
        ];
    }

    private function getBalanceLabel(): string
    {
        $user = Auth::user();

        if (! $user) {
            return 'Balance';
        }

        return $user->hasAnyRole(['super_admin', 'admin', 'manager'])
            ? 'System Balance'
            : 'My Balance';
    }

    private function getFormattedBalance(): string
    {
        $user = Auth::user();

        if (! $user) {
            return '৳0';
        }

        $isAdmin = $user->hasAnyRole(['super_admin', 'admin', 'manager']);
        $data = $this->getCachedAmounts($user, $isAdmin);

        return '৳'.number_format($data['balance'], 0);
    }

    private function shouldShowBalance(): bool
    {
        return Auth::check();
    }
}
