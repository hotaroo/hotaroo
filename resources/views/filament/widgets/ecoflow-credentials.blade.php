<x-filament::widget>
    <x-filament::card>
            <div class="flex gap-3">
                <x-notifications::icon icon="heroicon-o-exclamation-circle" color="warning" />
                <div class="flex h-6 items-center text-sm font-medium text-gray-500">
                    <p>EcoFlow API credentials missing</p>
                </div>
            </div>
            <div class="prose mt-4">
                <p>To apply for your EcoFlow API credentials please write a short email to their awesome support at <a href="mailto:support@ecoflow.com">support@ecoflow.com</a> including</p>
                <ul>
                    <li>the serial number of your power station and</li>
                    <li>the email address of your EcoFlow account.</li>
                </ul>
            </div>
            <div class="flex flex-wrap items-center gap-4 justify-end mt-7">
                <x-filament::button tag="a" href="mailto:support@ecoflow.com" color="secondary">
                    Write email
                </x-filament::button>
                <x-filament::button tag="a" href="{{ $widgetData['account_link'] }}" color="primary">
                    Enter credentials
                </x-filament::button>
            </div>
    </x-filament::card>
</x-filament::widget>
