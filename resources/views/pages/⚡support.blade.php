<?php

use Livewire\Attributes\Title;
use Livewire\Component;
use App\Http\Controllers\QrGenerator;

new #[Title('Support Us')] class extends Component
{
    public string $btcAddress = '';
    public string $ethAddress = '';
    public string $xmrAddress = '';

    public string $btcQr = '';
    public string $ethQr = '';
    public string $xmrQr = '';

    public function mount()
    {
        // Fetch addresses from env
        $this->btcAddress = trim(env('BTC_ADDRESS', ''));
        $this->ethAddress = trim(env('ETH_ADDRESS', ''));
        $this->xmrAddress = trim(env('XMR_ADDRESS', env('MONERO_ADDRESS', '')));

        // Generate QR codes using QrGenerator controller
        $qr = new QrGenerator();

        if ($this->btcAddress) {
            try {
                $this->btcQr = $qr->generateDataUri(
                    data: 'bitcoin:' . $this->btcAddress,
                    size: 200,
                    margin: 5,
                    fgColor: '#f7931a', // Bitcoin Gold
                    bgColor: '#ffffff',
                    format: 'svg'
                );
            } catch (\Exception $e) {
                // Fail-safe
                $this->btcQr = '';
            }
        }

        if ($this->ethAddress) {
            try {
                $this->ethQr = $qr->generateDataUri(
                    data: 'ethereum:' . $this->ethAddress,
                    size: 200,
                    margin: 5,
                    fgColor: '#627eea', // Ethereum Purple
                    bgColor: '#ffffff',
                    format: 'svg'
                );
            } catch (\Exception $e) {
                $this->ethQr = '';
            }
        }

        if ($this->xmrAddress) {
            try {
                $this->xmrQr = $qr->generateDataUri(
                    data: 'monero:' . $this->xmrAddress,
                    size: 200,
                    margin: 5,
                    fgColor: '#ff6600', // Monero Dark Orange
                    bgColor: '#ffffff',
                    format: 'svg'
                );
            } catch (\Exception $e) {
                $this->xmrQr = '';
            }
        }
    }
};
?>

<div class="min-h-screen pb-16 space-y-12">

    {{-- Page Header --}}
    <div>
        <div class="flex items-center gap-3 mb-2">
            <div class="p-2.5 bg-pink-50 dark:bg-pink-900/20 rounded-xl text-pink-600 dark:text-pink-400">
                <flux:icon icon="heart" variant="solid" class="size-7" />
            </div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white tracking-tight">Support {{ config('app.name') }}</h1>
        </div>
        <p class="text-sm text-zinc-500 dark:text-zinc-400 ml-14 max-w-2xl">
            {{ config('app.name') }} is an open-source, free-to-use secure workstation. If you find our offline cryptographic, metadata forensics, and cloaked routing tools helpful, please consider supporting the project to cover hosting and onion service node operations.
        </p>
    </div>

    {{-- Donation Cards Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

        {{-- 1. Bitcoin Card --}}
        <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-sm p-6 flex flex-col justify-between space-y-6 hover:border-zinc-300 dark:hover:border-zinc-700 transition">
            <div class="space-y-4">
                {{-- Coin Header --}}
                <div class="flex items-center gap-3">
                    <div class="p-2 rounded-xl bg-amber-500/10 text-amber-500 font-extrabold text-lg flex items-center justify-center size-10 shadow-inner">
                        ₿
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-zinc-900 dark:text-white uppercase tracking-wider">Bitcoin</h3>
                        <p class="text-[10px] text-zinc-400 font-semibold tracking-widest uppercase">BTC network</p>
                    </div>
                </div>

                {{-- QR Code Preview --}}
                <div class="flex justify-center items-center p-3 border border-zinc-100 dark:border-zinc-800/60 rounded-xl">
                    @if($btcQr)
                    <img src="{{ $btcQr }}" alt="Bitcoin QR Code" class="size-40 rounded-lg shadow-sm bg-white p-2 select-none" />
                    @else
                    <div class="size-40 flex flex-col items-center justify-center text-center space-y-2 text-zinc-400 dark:text-zinc-600">
                        <flux:icon icon="qr-code" class="size-8 opacity-40" />
                        <span class="text-[10px] font-medium">QR Code unavailable</span>
                    </div>
                    @endif
                </div>

                {{-- Address Field --}}
                <div class="space-y-2" x-data="{ copied: false, address: '{{ $btcAddress }}' }">
                    <div class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">Wallet Address</div>
                    <div class="font-mono text-xs break-all bg-zinc-50 dark:bg-zinc-950/60 border border-zinc-200 dark:border-zinc-800 p-3 rounded-xl text-zinc-700 dark:text-zinc-300 select-all min-h-[50px] flex items-center">
                        @if($btcAddress)
                        {{ $btcAddress }}
                        @else
                        <span class="text-zinc-400 dark:text-zinc-600 italic">Not configured</span>
                        @endif
                    </div>

                    @if($btcAddress)
                    <button
                        @click="navigator.clipboard.writeText(address); copied = true; setTimeout(() => copied = false, 2000)"
                        class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-zinc-900 hover:bg-zinc-800 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-white rounded-xl text-xs font-bold transition shadow-sm">
                        <span x-show="!copied" class="flex items-center gap-1.5">
                            <flux:icon icon="clipboard" class="size-3.5" />
                            Copy Address
                        </span>
                        <span x-show="copied" class="flex items-center gap-1.5 text-green-400" x-cloak>
                            <flux:icon icon="check" class="size-3.5" />
                            Copied!
                        </span>
                    </button>
                    @else
                    <div class="w-full text-center py-2 border border-dashed border-zinc-200 dark:border-zinc-800 rounded-xl text-[10px] font-medium text-zinc-400 dark:text-zinc-600">
                        Configure BTC_ADDRESS in .env
                    </div>
                    @endif
                </div>
            </div>
        </div>



        {{-- 3. Monero Card --}}
        <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-sm p-6 flex flex-col justify-between space-y-6 hover:border-zinc-300 dark:hover:border-zinc-700 transition">
            <div class="space-y-4">
                {{-- Coin Header --}}
                <div class="flex items-center gap-3">
                    <div class="p-2 rounded-xl bg-orange-500/10 text-orange-500 font-extrabold text-lg flex items-center justify-center size-10 shadow-inner">
                        ɱ
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-zinc-900 dark:text-white uppercase tracking-wider">Monero</h3>
                        <p class="text-[10px] text-zinc-400 font-semibold tracking-widest uppercase">XMR network</p>
                    </div>
                </div>

                {{-- QR Code Preview --}}
                <div class="flex justify-center items-center p-3  border border-zinc-100 dark:border-zinc-800/60 rounded-xl">
                    @if($xmrQr)
                    <img src="{{ $xmrQr }}" alt="Monero QR Code" class="size-40 rounded-lg shadow-sm bg-white p-2 select-none" />
                    @else
                    <div class="size-40 flex flex-col items-center justify-center text-center space-y-2 text-zinc-400 dark:text-zinc-600">
                        <flux:icon icon="qr-code" class="size-8 opacity-40" />
                        <span class="text-[10px] font-medium">QR Code unavailable</span>
                    </div>
                    @endif
                </div>

                {{-- Address Field --}}
                <div class="space-y-2" x-data="{ copied: false, address: '{{ $xmrAddress }}' }">
                    <div class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">Wallet Address</div>
                    <div class="font-mono text-xs break-all bg-zinc-50 dark:bg-zinc-950/60 border border-zinc-200 dark:border-zinc-800 p-3 rounded-xl text-zinc-700 dark:text-zinc-300 select-all min-h-[50px] flex items-center">
                        @if($xmrAddress)
                        {{ $xmrAddress }}
                        @else
                        <span class="text-zinc-400 dark:text-zinc-600 italic">Not configured</span>
                        @endif
                    </div>

                    @if($xmrAddress)
                    <button
                        @click="navigator.clipboard.writeText(address); copied = true; setTimeout(() => copied = false, 2000)"
                        class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-zinc-900 hover:bg-zinc-800 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-white rounded-xl text-xs font-bold transition shadow-sm">
                        <span x-show="!copied" class="flex items-center gap-1.5">
                            <flux:icon icon="clipboard" class="size-3.5" />
                            Copy Address
                        </span>
                        <span x-show="copied" class="flex items-center gap-1.5 text-green-400" x-cloak>
                            <flux:icon icon="check" class="size-3.5" />
                            Copied!
                        </span>
                    </button>
                    @else
                    <div class="w-full text-center py-2 border border-dashed border-zinc-200 dark:border-zinc-800 rounded-xl text-[10px] font-medium text-zinc-400 dark:text-zinc-600">
                        Configure XMR_ADDRESS in .env
                    </div>
                    @endif
                </div>
            </div>
        </div>

    </div>

    {{-- Safe & Secure Sandboxed Donation Info --}}
    <div class="bg-zinc-50 dark:bg-zinc-900/40 rounded-3xl border border-zinc-200 dark:border-zinc-800/80 p-8 sm:p-10 space-y-6">
        <div class="flex items-center gap-3">
            <div class="p-1.5 bg-violet-100 dark:bg-violet-950/40 rounded-lg text-violet-600 dark:text-violet-400">
                <flux:icon icon="information-circle" class="size-5" />
            </div>
            <h2 class="text-sm font-bold text-zinc-700 dark:text-zinc-200 uppercase tracking-widest">Privacy & Verification Notes</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 text-xs leading-relaxed text-zinc-600 dark:text-zinc-400">
            <p>
                To protect your privacy when donating, we highly recommend using **Monero (XMR)** for untraceable transactions. Make sure to verify the destination wallet address against multiple sources if you are transferring large amounts of cryptocurrency.
            </p>
            <p>
                All generation of the payment URIs and QR code rendering is processed entirely via local SVG creation on the backend, meaning your browser doesn't load any external third-party tracker APIs (like google charts or external web-trackers) to show the QR code.
            </p>
        </div>
    </div>

</div>