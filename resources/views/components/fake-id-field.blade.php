@props([
'section' => '',
'field' => '',
'label' => '',
'icon' => 'document-text',
'mono' => false,
'secret' => false, // blur value, click to reveal
'badge' => false, // render as badge pill
])

<div
    class="group flex items-center gap-3 px-2 py-1.5 hover:bg-zinc-100 dark:hover:bg-zinc-800/40 rounded-lg">

    {{-- Icon + Label --}}
    <div class="flex items-center gap-1.5 w-36 shrink-0">
        <flux:icon icon="{{ $icon }}" class="size-3.5 text-zinc-400 dark:text-zinc-500 shrink-0" />
        <span class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider truncate select-none">
            {{ $label }}
        </span>
    </div>

    {{-- Value --}}
    <div class="flex-1 min-w-0 flex items-center justify-end gap-2">
        @if($secret)
        <span
            x-data="{ revealed: false }"
            x-on:click="revealed = !revealed"
            class="cursor-pointer">
            <span
                x-show="!revealed"
                x-text="'•'.repeat(8)"
                class="text-sm font-mono tracking-widest text-zinc-400 dark:text-zinc-500 select-none">
            </span>
            <span
                x-show="revealed"
                x-text="$wire.identity?.{{ $section }}?.{{ $field }} ?? '–'"
                @class(['text-sm truncate text-right', 'font-mono text-zinc-700 dark:text-zinc-300'=> $mono, 'font-medium text-zinc-800 dark:text-zinc-100' => !$mono])>
            </span>
        </span>
        @elseif($badge)
        <span
            x-text="$wire.identity?.{{ $section }}?.{{ $field }} ?? '–'"
            class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold bg-violet-100 dark:bg-violet-900/40 text-violet-700 dark:text-violet-300 max-w-full truncate">
        </span>
        @else
        <span
            x-text="$wire.identity?.{{ $section }}?.{{ $field }} ?? '–'"
            @class([ 'text-sm truncate text-right max-w-full' , 'font-mono text-zinc-600 dark:text-zinc-400'=> $mono,
            'font-medium text-zinc-800 dark:text-zinc-100' => !$mono,
            ])>
        </span>
        @endif

        {{-- Copy button --}}
        <button
            x-on:click="copyField('{{ $section }}', '{{ $field }}', '{{ $label }}')"
            class="shrink-0 ml-1 p-1.5 rounded-lg opacity-0 group-hover:opacity-100 hover:bg-violet-100 dark:hover:bg-violet-900/40"
            title="Copy {{ $label }}">
            <flux:icon
                x-show="!(copiedSection === '{{ $section }}' && copiedField === '{{ $field }}')"
                icon="clipboard"
                class="size-3.5 text-zinc-400 hover:text-violet-500" />
            <flux:icon
                x-show="copiedSection === '{{ $section }}' && copiedField === '{{ $field }}'"
                icon="check"
                class="size-3.5 text-green-500" />
        </button>
    </div>
</div>