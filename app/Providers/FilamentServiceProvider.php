<?php

namespace App\Providers;

use App\Filament\Resources\UserResource;
use Filament\Facades\Filament;
use Filament\Forms\Components\DateTimePicker;
use Filament\Navigation\UserMenuItem;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Vite;
use Illuminate\Support\ServiceProvider;

class FilamentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Filament::serving(function () {
            Filament::registerTheme(
                app(Vite::class)('resources/css/filament.css'),
            );
        });

        TextColumn::configureUsing(
            fn (TextColumn $column) => $column->timezone(
                auth()->user()->timezone
            )
        );

        DateTimePicker::configureUsing(
            fn (DateTimePicker $component) => $component
                ->displayFormat(
                    auth()->user()->date_format
                    .' '.auth()->user()->time_format
                )
        );

        Filament::serving(function () {
            Filament::registerUserMenuItems([
                'account' => UserMenuItem::make()->url(
                    UserResource::getUrl('edit', ['record' => auth()->user()])
                ),
                'logout' => UserMenuItem::make()->url(route('logout')),
            ]);
        });

        Filament::registerRenderHook(
            'footer.end',
            fn (): View => view('filament.pages.footer'),
        );
    }
}