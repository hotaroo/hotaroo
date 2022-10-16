<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\UserResource;
use Filament\Widgets\Widget;

class EcoFlowCredentialsWidget extends Widget
{
    public $widgetData;

    protected static string $view = 'filament.widgets.ecoflow-credentials';

    protected static ?int $sort = 0;

    public static function canView(): bool
    {
        $user = auth()->user();

        return ! $user->ecoflow_key || ! $user->ecoflow_secret;
    }

    public function mount(): void
    {
        $this->widgetData = [
            'account_link' => UserResource::getUrl(
                'edit',
                ['record' => auth()->user()]
            ).'#ecoflow',
        ];
    }
}
