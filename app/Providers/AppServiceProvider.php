<?php

namespace App\Providers;

use App\Models\Ticket;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
        $this->registerSidebarComposer();
    }

    protected function registerSidebarComposer(): void
    {
        View::composer('*', function ($view) {
            if (!str_contains($view->getName(), 'sidebar')) {
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
                $isStaff = $user->hasAnyRole(['Admin', 'Agente']);

                $ticketQuery = Ticket::query()->when(!$isStaff, fn($q) => $q->where('created_by', $user->id));

                $sidebarStats = [
                    'total' => (clone $ticketQuery)->count(),
                    'open' => (clone $ticketQuery)->whereHas('status', fn($q) => $q->where('name', 'Abierto'))->count(),
                    'in_progress' => (clone $ticketQuery)->whereHas('status', fn($q) => $q->where('name', 'En Progreso'))->count(),
                    'resolved' => (clone $ticketQuery)->whereHas('status', fn($q) => $q->where('name', 'Cerrado'))->count(),
                ];
            }

            $view->with('sidebarStats', $sidebarStats);
        });
    }

    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

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
            : null
        );
    }
}
