@php
    $percent = (int) ($getState() ?? 0);
@endphp
<div class="w-full flex items-center gap-2">
    <div class="w-32 bg-gray-200 dark:bg-gray-800 rounded h-2">
        <div class="bg-emerald-500 dark:bg-primary-400 h-2 rounded transition-all duration-300"
             style="width: {{ $percent }}%;">
        </div>
    </div>
    <span class="text-xs font-medium text-gray-700 dark:text-gray-300">{{ $percent }}%</span>
</div>
