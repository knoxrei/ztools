@props([
    'title'   => '',
    'icon'    => 'squares-2x2',
    'section' => '',
])

<div class="h-full">
    {{-- Flat Section Header --}}
    <div class="flex items-center justify-between pb-2 mb-2 border-b border-zinc-200 dark:border-zinc-800">
        <div class="flex items-center gap-2">
            <flux:icon icon="{{ $icon }}" class="size-4 text-zinc-400 dark:text-zinc-500 shrink-0" />
            <p class="text-[11px] font-bold text-zinc-700 dark:text-zinc-300 uppercase tracking-wider">{{ $title }}</p>
        </div>
    </div>
    
    {{-- List Body --}}
    <div class="space-y-0.5">
        {{ $slot }}
    </div>
</div>