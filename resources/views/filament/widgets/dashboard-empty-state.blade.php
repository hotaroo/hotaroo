<x-filament::widget>
    <a href="{{ $widgetData['device_link'] }}">
        <div class="flex items-center justify-center p-4 w-full">
            <div class="flex flex-1 flex-col items-center justify-center p-6 mx-auto space-y-6 text-center">
                <div class="flex items-center justify-center w-16 h-16 text-primary-500 rounded-full bg-primary-50">
                    <x-dynamic-component component="heroicon-o-plus" class="w-6 h-6"/>
                </div>
                <div class="max-w-md space-y-1">
                    <h2 class="filament-tables-empty-state-heading text-xl font-bold tracking-tight">
                        Add your device
                    </h2>
                </div>
            </div>
        </div>
    </a>
</x-filament::widget>
