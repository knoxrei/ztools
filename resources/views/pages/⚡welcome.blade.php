<?php

use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Home')] class extends Component
{
    public string $onionUrl = '';
    public string $clearnetUrl = '';

    public function mount(): void
    {
        $this->onionUrl = env('TOR_CONNECTION', '');
        $this->clearnetUrl = env('CLEARNET_CONNECTION') ?: config('app.url');
    }
};
?>

<div class="min-h-screen space-y-16 pb-16">

    {{-- Hero Section --}}
    <div class="relative bg-zinc-900  overflow-hidden  p-8 sm:p-12 md:p-16 flex flex-col md:flex-row items-center gap-10">

        <div class="space-y-6 flex-1 relative z-1">


            <h1 class="text-3xl sm:text-4xl md:text-5xl font-extrabold text-white tracking-tight leading-tight">
                Welcome to {{ config('app.name') }}
            </h1>

            <p class="text-zinc-400 text-sm sm:text-base leading-relaxed max-w-xl">
                {{ config('app.name') }} is a premium, zero-knowledge offline cryptography and file diagnostics workspace. Designed for privacy advocates, security researchers, and Tor network users.
            </p>

            <div class="flex flex-wrap gap-4 pt-2">
                <flux:button :href="route('tools')" variant="filled" class="bg-violet-600 hover:bg-violet-700 text-white font-bold px-6 py-2.5 rounded-xl text-sm shadow transition" wire:navigate>
                    Launch Tools Suite
                </flux:button>


                <a href="{{ $clearnetUrl }}" target="_blank" class="flex items-center gap-2 px-5 py-2.5 bg-zinc-800 hover:bg-zinc-700 text-zinc-300 font-semibold border border-zinc-700 hover:border-zinc-600 rounded-xl text-sm transition shadow-sm">
                    <flux:icon icon="globe-alt" class="size-4 text-sky-400" />
                    Clearnet Version
                </a>



                <a href="{{ $onionUrl }}" target="_blank" class="flex items-center gap-2 px-5 py-2.5 bg-zinc-800 hover:bg-zinc-700 text-zinc-300 font-semibold border border-zinc-700 hover:border-zinc-600 rounded-xl text-sm transition shadow-sm">
                    <flux:icon icon="shield-check" class="size-4 text-violet-400" />
                    Tor Onion Version
                </a>

            </div>
        </div>

        <div class="w-full md:w-72 flex justify-center shrink-0">
            <div class="relative p-6   rounded-2xl flex flex-col items-center justify-center text-center space-y-4">
                <div class="p-3 ">
                    <img src="{{ asset('/logo.webp') }}">
                </div>
                <div class="space-y-1">
                    <h3 class="text-sm font-bold text-white tracking-wide">Secure Sandbox</h3>
                    <p class="text-xs text-zinc-400 leading-normal max-w-[200px]">
                        All operations run in-memory with zero local logs.
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- What is app name section --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 items-stretch">

        {{-- Card 1 --}}
        <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-sm p-6 space-y-4 flex flex-col justify-between">
            <div class="space-y-3">
                <div class="p-2.5 bg-violet-50 dark:bg-violet-900/30 rounded-xl text-violet-600 dark:text-violet-400 w-fit">
                    <flux:icon icon="lock-closed" class="size-5" />
                </div>
                <h3 class="text-sm font-bold text-zinc-800 dark:text-white uppercase tracking-wider">Zero-Knowledge Cryptography</h3>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 leading-relaxed">
                    Encrypt and decrypt messages using top-tier block ciphers like AES-256, Twofish, ChaCha20, or Blowfish. Key generation and hashing are processed entirely client-side.
                </p>
            </div>
        </div>

        {{-- Card 2 --}}
        <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-sm p-6 space-y-4 flex flex-col justify-between">
            <div class="space-y-3">
                <div class="p-2.5 bg-violet-50 dark:bg-violet-900/30 rounded-xl text-violet-600 dark:text-violet-400 w-fit">
                    <flux:icon icon="eye" class="size-5" />
                </div>
                <h3 class="text-sm font-bold text-zinc-800 dark:text-white uppercase tracking-wider">File & Metadata Forensics</h3>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 leading-relaxed">
                    Deeply analyze documents and images. Extract EXIF profiles, decode raw ExifTool tag dumps, map GPS markers, and cross-reference binary magic bytes to detect spoofed file extensions.
                </p>
            </div>
        </div>

        {{-- Card 3 --}}
        <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-sm p-6 space-y-4 flex flex-col justify-between">
            <div class="space-y-3">
                <div class="p-2.5 bg-violet-50 dark:bg-violet-900/30 rounded-xl text-violet-600 dark:text-violet-400 w-fit">
                    <flux:icon icon="link" class="size-5" />
                </div>
                <h3 class="text-sm font-bold text-zinc-800 dark:text-white uppercase tracking-wider">Cloaked Redirection</h3>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 leading-relaxed">
                    Shorten URLs with disposable burn-on-read capabilities, access password walls, click limits, tracking parameters cleaner, and OpenGraph metadata cloaking.
                </p>
            </div>
        </div>

    </div>

    {{-- Mid-Page Ad Banner --}}
    <div class="flex justify-center py-2">
        <div id="banner-place-468-1" class="w-[468px] h-[60px] bg-zinc-800/30 rounded border border-zinc-800/50 flex items-center justify-center overflow-hidden"></div>
    </div>

    {{-- Detailed Tor Network Statement --}}
    <div class="bg-zinc-50 dark:bg-zinc-900/40 rounded-3xl border border-zinc-200 dark:border-zinc-800/80 p-8 sm:p-10 space-y-6">
        <div class="flex items-center gap-3">
            <div class="p-1.5 bg-violet-100 dark:bg-violet-950/40 rounded-lg text-violet-600 dark:text-violet-400">
                <flux:icon icon="information-circle" class="size-5" />
            </div>
            <h2 class="text-sm font-bold text-zinc-700 dark:text-zinc-200 uppercase tracking-widest">Tor & Hidden Service Architecture</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 text-xs leading-relaxed text-zinc-600 dark:text-zinc-400">
            <p>
                {{ config('app.name') }} is built from the ground up to support the security and performance requirements of the Tor browser ecosystem. Pages are designed without heavy Javascript dependencies, bloated animations, or tracking pixels that could leak browser signatures. Redirection structures are fully session-based, keeping search footprint zero.
            </p>
            <p>
                By configuring network domains inside the environment profile, {{ config('app.name') }} routes users dynamically between clearweb endpoints and the Tor onion service. This makes {{ config('app.name') }} the ideal, trusted local companion for privacy compliance, secure document transfer verification, and security testing.
            </p>
        </div>
    </div>

    {{-- Bottom Ad Banner --}}
    <div class="flex justify-center py-4">
        <div id="banner-place-468-2" class="w-[468px] h-[60px] bg-zinc-800/30 rounded border border-zinc-800/50 flex items-center justify-center overflow-hidden"></div>
    </div>

    <script>
        function initializeBanners() {
            if (typeof getBanners === 'function') {
                getBanners("http://admate3tczgp6digew7jpzcosq52rs7anru53imwqimron27emq7dbqd.onion/api/get-banner/rz3qL7UuuKppCuxG/type/468-60/count/2");
            } else {
                let retries = 0;
                const interval = setInterval(() => {
                    retries++;
                    if (typeof getBanners === 'function') {
                        getBanners("http://admate3tczgp6digew7jpzcosq52rs7anru53imwqimron27emq7dbqd.onion/api/get-banner/rz3qL7UuuKppCuxG/type/468-60/count/2");
                        clearInterval(interval);
                    } else if (retries > 30) {
                        clearInterval(interval);
                    }
                }, 100);
            }
        }
        document.addEventListener('livewire:navigated', initializeBanners);
        initializeBanners();
    </script>
</div>