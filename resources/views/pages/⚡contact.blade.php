<?php

use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Contact Us')] class extends Component
{
    public string $email = '';
    public string $session = '';
    public string $telegram = '';

    public function mount()
    {
        $this->email    = config('app.contact.email');
        $this->session  = config('app.contact.session');
    }
};
?>

<div class="min-h-screen space-y-10 pb-16">

    {{-- Page Header --}}
    <div class="flex items-center gap-3 mb-2">

        <div>
            <h1 class="text-xl font-bold text-zinc-900 dark:text-white tracking-tight">Contact Us</h1>
            <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">
                Reach the {{ config('app.name') }} team through our verified, encrypted channels.
            </p>
        </div>
    </div>

    {{-- Contact Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">

        {{-- Session Messenger --}}
        <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800 overflow-hidden flex flex-col">

            <div class="p-5 flex flex-col flex-1 space-y-4">

                <div class="flex items-center gap-3">
                    <div class="p-2 bg-emerald-50 dark:bg-emerald-900/20 rounded-lg text-emerald-600 dark:text-emerald-400">
                        <flux:icon icon="shield-check" class="size-5" />
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-zinc-900 dark:text-white">Session Messenger</h3>
                    </div>
                </div>



                <div class="flex-1"></div>

                <div class="space-y-2" x-data="{ copied: false, val: '{{ $session }}' }">
                    <div class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">Session ID</div>
                    <div class="font-mono text-[11px] break-all bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 p-3 rounded-lg text-zinc-700 dark:text-zinc-300 select-all min-h-[48px] flex items-center">
                        @if($session)
                        {{ $session }}
                        @else
                        <span class="text-zinc-400 dark:text-zinc-600 italic">Not configured</span>
                        @endif
                    </div>

                    @if($session)
                    <button
                        @click="navigator.clipboard.writeText(val); copied = true; setTimeout(() => copied = false, 2000)"
                        class="w-full flex items-center justify-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-semibold">
                        <span x-show="!copied" class="flex items-center gap-1.5">
                            <flux:icon icon="clipboard-document" class="size-3.5" />
                            Copy Session ID
                        </span>
                        <span x-show="copied" class="flex items-center gap-1.5" x-cloak>
                            <flux:icon icon="check" class="size-3.5" />
                            Copied
                        </span>
                    </button>
                    @endif
                </div>

            </div>
        </div>


        {{-- Email --}}
        <div class="bg-white dark:bg-zinc-900 rounded-xl border border-zinc-200 dark:border-zinc-800 overflow-hidden flex flex-col">

            <div class="p-5 flex flex-col flex-1 space-y-4">

                <div class="flex items-center gap-3">
                    <div class="p-2 bg-violet-50 dark:bg-violet-900/20 rounded-lg text-violet-600 dark:text-violet-400">
                        <flux:icon icon="envelope" class="size-5" />
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-zinc-900 dark:text-white">Email</h3>
                    </div>
                </div>

                <div class="flex-1"></div>

                <div class="space-y-2" x-data="{ copied: false, val: '{{ $email }}' }">
                    <div class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">Email Address</div>
                    <div class="font-mono text-[11px] break-all bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 p-3 rounded-lg text-zinc-700 dark:text-zinc-300 select-all min-h-[48px] flex items-center">
                        @if($email)
                        {{ $email }}
                        @else
                        <span class="text-zinc-400 dark:text-zinc-600 italic">Not configured</span>
                        @endif
                    </div>

                    @if($email)
                    <div class="flex gap-2">
                        <a href="mailto:{{ $email }}"
                            class="flex-1 flex items-center justify-center gap-1.5 px-3 py-2 bg-violet-600 hover:bg-violet-700 text-white rounded-lg text-xs font-semibold">
                            <flux:icon icon="envelope" class="size-3.5" />
                            Send Email
                        </a>
                        <button
                            @click="navigator.clipboard.writeText(val); copied = true; setTimeout(() => copied = false, 2000)"
                            class="flex-1 flex items-center justify-center gap-1.5 px-3 py-2 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700 rounded-lg text-xs font-semibold">
                            <span x-show="!copied" class="flex items-center gap-1.5">
                                <flux:icon icon="clipboard-document" class="size-3.5" />
                                Copy
                            </span>
                            <span x-show="copied" class="flex items-center gap-1.5 text-emerald-500" x-cloak>
                                <flux:icon icon="check" class="size-3.5" />
                                Copied
                            </span>
                        </button>
                    </div>
                    @endif
                </div>

            </div>
        </div>

    </div>

</div>