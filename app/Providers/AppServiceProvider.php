<?php

namespace App\Providers;

use App\Enums\TicketStatus;
use App\Models\Ticket;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->configureDefaults();
        $this->registerSidebarComposer();
    }

    protected function registerSidebarComposer(): void
    {
        View::composer('*', function ($view) {
            if (! str_contains($view->getName(), 'sidebar')) {
                return;
            }

            $sidebarStats = [
                'total' => 0,
                'open' => 0,
                'in_progress' => 0,
                'resolved' => 0,
            ];

            if (Auth::check()) {
                $user = Auth::user();
                $cacheKey = "sidebar:{$user->id}";

                $sidebarStats = Cache::remember($cacheKey, 60, function () use ($user) {
                    $ticketQuery = Ticket::query()->visibleTo($user);

                    return [
                        'total' => (clone $ticketQuery)->count(),
                        'open' => (clone $ticketQuery)->where('status', TicketStatus::Abierto)->count(),
                        'in_progress' => (clone $ticketQuery)->where('status', TicketStatus::EnProgreso)->count(),
                        'resolved' => (clone $ticketQuery)->where('status', TicketStatus::Cerrado)->count(),
                    ];
                });
            }

            $view->with('sidebarStats', $sidebarStats);
        });
    }

    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        \Illuminate\Support\Carbon::setLocale(config('app.locale'));

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null);
    }
}
