<x-filament-panels::page>
    @if($showProgress)
        <div wire:poll.1s="updateProgress" class="flex items-center space-x-2 mb-4">
            <div class="w-full bg-gray-200 rounded-full h-2 dark:bg-gray-700">
                <div class="bg-emerald-600 h-2 rounded-full transition-all duration-500 ease-in-out"
                     style="width: {{$progress}}%"></div>
            </div>
            <span>{{$progress}}%</span>
        </div>
    @endif
    <div>
        {{ $this->table }}
    </div>
</x-filament-panels::page>
