<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\DeviceResource;
use Filament\Widgets\Widget;

class DashboardEmptyState extends Widget
{
    public $widgetData;

    protected static string $view = 'filament.widgets.dashboard-empty-state';

    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        $user = auth()->user();

        return $user->ecoflow_key
               && $user->ecoflow_secret
               && $user->devices->isEmpty();
    }

    public function mount(): void
    {
        $this->widgetData = [
            'device_link' => DeviceResource::getUrl('create'),
        ];
    }
}
