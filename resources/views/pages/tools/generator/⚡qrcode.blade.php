<?php

use App\Http\Controllers\QrGenerator;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('QR Code Generator')] class extends Component
{
    // ── Input ───────────────────────────────────────────────────────────────
    public string $data = '';

    // ── Styling ─────────────────────────────────────────────────────────────
    public string $fgColor   = '#000000';
    public string $bgColor   = '#ffffff';
    public string $format    = 'png';   // png | svg

    // ── QR Settings ─────────────────────────────────────────────────────────
    public int    $size       = 300;
    public int    $margin     = 10;

    // ── Quick-fill presets ──────────────────────────────────────────────────
    public string $activePreset = 'text';

    // ── State ───────────────────────────────────────────────────────────────
    public ?string $qrDataUri  = null;
    public bool    $isLoading  = false;
    public ?string $flashMsg   = null;

    // ── Preset form fields ──────────────────────────────────────────────────
    public string $wifiSsid   = '';
    public string $wifiPass   = '';
    public string $wifiSec    = 'WPA'; // WPA | WEP | nopass
    public string $wifiHide   = 'false';
    public string $emailTo    = '';
    public string $emailSub   = '';
    public string $emailBody  = '';
    public string $smsTel     = '';
    public string $smsMsgBody = '';
    public string $telNumber  = '';
    public string $geoLat     = '';
    public string $geoLon     = '';
    public string $vcardName  = '';
    public string $vcardEmail = '';
    public string $vcardPhone = '';

    // ─────────────────────────────────────────────────────────────────────────
    // Lifecycle hooks — build $data from preset fields then auto-generate
    // ─────────────────────────────────────────────────────────────────────────

    /** Reset everything when the user switches preset tab */
    public function updatedActivePreset(): void
    {
        $this->resetPresetFields();
        $this->data       = '';
        $this->qrDataUri  = null;
        $this->flashMsg   = null;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Private builders — build $data string from preset fields
    // ─────────────────────────────────────────────────────────────────────────

    private function buildDataFromPreset(): void
    {
        $this->data = match ($this->activePreset) {
            'wifi'  => "WIFI:T:{$this->wifiSec};S:{$this->wifiSsid};P:{$this->wifiPass};H:{$this->wifiHide};;",
            'email' => "mailto:{$this->emailTo}?subject={$this->emailSub}&body={$this->emailBody}",
            'sms'   => "SMSTO:{$this->smsTel}:{$this->smsMsgBody}",
            'tel'   => "tel:{$this->telNumber}",
            'geo'   => "geo:{$this->geoLat},{$this->geoLon}",
            'vcard' => implode("\n", [
                'BEGIN:VCARD',
                'VERSION:3.0',
                "FN:{$this->vcardName}",
                "EMAIL:{$this->vcardEmail}",
                "TEL:{$this->vcardPhone}",
                'END:VCARD',
            ]),
            default => $this->data, // text & url: $data is bound directly
        };
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Public actions
    // ─────────────────────────────────────────────────────────────────────────

    /** Generate QR code on button click */
    public function generate(): void
    {
        // Build $data from preset fields before validating
        $this->buildDataFromPreset();

        $this->validate([
            'data'    => 'required|string|max:2953',
            'size'    => 'integer|min:100|max:1000',
            'margin'  => 'integer|min:0|max:100',
            'fgColor' => ['required', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'bgColor' => ['required', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'format'  => 'in:png,svg',
        ]);

        $generator = new QrGenerator();

        $this->qrDataUri = $generator->generateDataUri(
            data: $this->data,
            size: $this->size,
            margin: $this->margin,
            fgColor: $this->fgColor,
            bgColor: $this->bgColor,
            format: $this->format,
        );

        $this->flashMsg = null;
    }

    /** Reset everything */
    public function resetAll(): void
    {
        $this->resetPresetFields();
        $this->reset(['data', 'qrDataUri', 'flashMsg']);
        $this->fgColor      = '#000000';
        $this->bgColor      = '#ffffff';
        $this->size         = 300;
        $this->margin       = 10;
        $this->format       = 'png';
        $this->activePreset = 'text';
    }

    private function resetPresetFields(): void
    {
        $this->reset([
            'wifiSsid',
            'wifiPass',
            'wifiSec',
            'wifiHide',
            'emailTo',
            'emailSub',
            'emailBody',
            'smsTel',
            'smsMsgBody',
            'telNumber',
            'geoLat',
            'geoLon',
            'vcardName',
            'vcardEmail',
            'vcardPhone',
        ]);
        $this->wifiSec  = 'WPA';
        $this->wifiHide = 'false';
    }
};
?>

<div
    x-data="{
        get previewBg() { return $wire.bgColor; },
    }"
    class="min-h-screen"
    x-on:copy-to-clipboard.window="
        navigator.clipboard.writeText($event.detail.text)
            .then(() => { $wire.flashMsg = 'Data URI copied!'; setTimeout(() => $wire.flashMsg = null, 2500); })
    ">
    {{-- ─────────────────────────── Page Header ──────────────────────────── --}}
    <div class="mb-8">
        <div class="flex items-center gap-3 mb-2">
            <div class="p-2 rounded-xl">
                <flux:icon icon="qr-code" class="size-7 text-violet-600 dark:text-violet-400" />
            </div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white tracking-tight">QR Code Generator</h1>
        </div>
        <p class="text-sm text-zinc-500 dark:text-zinc-400 ml-14">
            Generate customisable QR codes for URLs, Wi-Fi, contacts, and more.
        </p>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-5 gap-6">

        {{-- ──────────────────────── Left Panel ─────────────────────────── --}}
        <div class="xl:col-span-3 space-y-5">

            {{-- ── Preset Tabs ── --}}
            <div class="mb-5">
                <div class="flex flex-wrap gap-1 p-1.5 bg-zinc-100 dark:bg-zinc-800/80 rounded-2xl border border-zinc-200 dark:border-zinc-700/60 w-full">
                    @foreach([
                        ['text', 'document-text', 'Text'],
                        ['url', 'link', 'URL'],
                        ['wifi', 'wifi', 'Wi-Fi'],
                        ['email', 'envelope', 'Email'],
                        ['sms', 'chat-bubble-left', 'SMS'],
                        ['tel', 'phone', 'Phone'],
                        ['geo', 'map-pin', 'Location'],
                        ['vcard', 'user', 'vCard'],
                    ] as [$key, $icon, $label])
                    <button
                        wire:click="$set('activePreset', '{{ $key }}')"
                        @class([
                            'flex-1 flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl text-sm font-semibold whitespace-nowrap min-w-[80px]',
                            'bg-white dark:bg-zinc-700 text-violet-600 dark:text-violet-400 shadow-sm font-bold' => $activePreset === $key,
                            'text-zinc-500 dark:text-zinc-400 hover:text-zinc-800 dark:hover:text-zinc-200' => $activePreset !== $key,
                        ])>
                        <flux:icon icon="{{ $icon }}" class="size-3.5" />
                        {{ $label }}
                    </button>
                    @endforeach
                </div>
            </div>

            {{-- ── Dynamic Input Fields ── --}}
            <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800/80 shadow-sm p-5 space-y-4">
                <div class="flex items-center gap-2 mb-1">
                    <div class="p-1.5 rounded-lg bg-violet-50 dark:bg-violet-900/30">
                        <flux:icon icon="document" class="size-3.5 text-violet-600 dark:text-violet-400" />
                    </div>
                    <p class="text-xs font-bold text-zinc-700 dark:text-zinc-200 uppercase tracking-widest">
                        Content Inputs
                    </p>
                </div>

                {{-- Text --}}
                @if($activePreset === 'text')
                <flux:textarea
                    label="Text / Raw Data"
                    wire:model.live.debounce.300ms="data"
                    placeholder="Enter any text, phone number, raw data…"
                    rows="4"
                    resize="vertical" />

                {{-- URL --}}
                @elseif($activePreset === 'url')
                <flux:input
                    label="Website URL"
                    wire:model.live.debounce.300ms="data"
                    placeholder="https://example.com"
                    type="url" />

                {{-- Wi-Fi --}}
                @elseif($activePreset === 'wifi')
                <div class="grid grid-cols-2 gap-4">
                    <flux:input label="SSID (Network Name)" wire:model.live.debounce.300ms="wifiSsid" placeholder="MyNetwork" />
                    <flux:input label="Password" wire:model.live.debounce.300ms="wifiPass" type="password" placeholder="••••••••" />
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <flux:select label="Security" wire:model.live="wifiSec">
                        <flux:select.option value="WPA">WPA/WPA2</flux:select.option>
                        <flux:select.option value="WEP">WEP</flux:select.option>
                        <flux:select.option value="nopass">None</flux:select.option>
                    </flux:select>
                    <flux:select label="Hidden Network?" wire:model.live="wifiHide">
                        <flux:select.option value="false">No</flux:select.option>
                        <flux:select.option value="true">Yes</flux:select.option>
                    </flux:select>
                </div>

                {{-- Email --}}
                @elseif($activePreset === 'email')
                <flux:input label="To" wire:model.live.debounce.300ms="emailTo" placeholder="recipient@example.com" type="email" />
                <flux:input label="Subject" wire:model.live.debounce.300ms="emailSub" placeholder="Hello!" />
                <flux:textarea label="Body" wire:model.live.debounce.300ms="emailBody" placeholder="Email body…" rows="3" />

                {{-- SMS --}}
                @elseif($activePreset === 'sms')
                <flux:input label="Phone Number" wire:model.live.debounce.300ms="smsTel" placeholder="+1234567890" />
                <flux:textarea label="Message" wire:model.live.debounce.300ms="smsMsgBody" placeholder="Your message…" rows="3" />

                {{-- Tel --}}
                @elseif($activePreset === 'tel')
                <flux:input label="Phone Number" wire:model.live.debounce.300ms="telNumber" placeholder="+1234567890" />

                {{-- Geo --}}
                @elseif($activePreset === 'geo')
                <div class="grid grid-cols-2 gap-4">
                    <flux:input label="Latitude" wire:model.live.debounce.300ms="geoLat" placeholder="-6.200000" />
                    <flux:input label="Longitude" wire:model.live.debounce.300ms="geoLon" placeholder="106.816666" />
                </div>

                {{-- vCard --}}
                @elseif($activePreset === 'vcard')
                <flux:input label="Full Name" wire:model.live.debounce.300ms="vcardName" placeholder="John Doe" />
                <div class="grid grid-cols-2 gap-4">
                    <flux:input label="Email" wire:model.live.debounce.300ms="vcardEmail" type="email" placeholder="john@example.com" />
                    <flux:input label="Phone" wire:model.live.debounce.300ms="vcardPhone" placeholder="+1234567890" />
                </div>
                @endif

                {{-- Encoded data preview --}}
                @if($data)
                <div class="mt-1 p-2 bg-zinc-50 dark:bg-zinc-800/60 rounded-lg border border-zinc-200 dark:border-zinc-700">
                    <p class="text-[10px] font-mono text-zinc-400 break-all leading-relaxed">{{ $data }}</p>
                </div>
                @endif
            </div>

            {{-- ── Appearance Settings ── --}}
            <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800/80 shadow-sm p-5 space-y-5">
                <div class="flex items-center gap-2 mb-1">
                    <div class="p-1.5 rounded-lg bg-violet-50 dark:bg-violet-900/30">
                        <flux:icon icon="paint-brush" class="size-3.5 text-violet-600 dark:text-violet-400" />
                    </div>
                    <p class="text-xs font-bold text-zinc-700 dark:text-zinc-200 uppercase tracking-widest">
                        Appearance Settings
                    </p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1.5">
                            Foreground
                        </label>
                        <div class="flex items-center gap-2">
                            <input
                                type="color"
                                wire:model.live.debounce.300ms="fgColor"
                                class="h-9 w-14 rounded-lg border border-zinc-300 dark:border-zinc-600 cursor-pointer bg-transparent p-0.5" />
                            <flux:input wire:model.live.debounce.300ms="fgColor" class="font-mono text-sm" maxlength="7" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1.5">
                            Background
                        </label>
                        <div class="flex items-center gap-2">
                            <input
                                type="color"
                                wire:model.live.debounce.300ms="bgColor"
                                class="h-9 w-14 rounded-lg border border-zinc-300 dark:border-zinc-600 cursor-pointer bg-transparent p-0.5" />
                            <flux:input wire:model.live.debounce.300ms="bgColor" class="font-mono text-sm" maxlength="7" />
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-4">
                    <flux:field>
                        <flux:label>Size (px)</flux:label>
                        <flux:input type="number" wire:model.live.debounce.300ms="size" min="100" max="1000" step="50" />
                    </flux:field>
                    <flux:field>
                        <flux:label>Margin (px)</flux:label>
                        <flux:input type="number" wire:model.live.debounce.300ms="margin" min="0" max="100" step="5" />
                    </flux:field>
                    <flux:field>
                        <flux:label>Format</flux:label>
                        <flux:select wire:model.live="format">
                            <flux:select.option value="png">PNG</flux:select.option>
                            <flux:select.option value="svg">SVG</flux:select.option>
                        </flux:select>
                    </flux:field>
                </div>
            </div>

            {{-- ── Color Presets ── --}}
            <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800/80 shadow-sm p-5 space-y-3">
                <div class="flex items-center gap-2 mb-1">
                    <div class="p-1.5 rounded-lg bg-violet-50 dark:bg-violet-900/30">
                        <flux:icon icon="swatch" class="size-3.5 text-violet-600 dark:text-violet-400" />
                    </div>
                    <p class="text-xs font-bold text-zinc-700 dark:text-zinc-200 uppercase tracking-widest">
                        Color Presets
                    </p>
                </div>
                <div class="flex flex-wrap gap-2">
                    @foreach([
                    ['#000000', '#ffffff', 'Classic'],
                    ['#1e3a5f', '#e8f4fd', 'Ocean'],
                    ['#2d5016', '#f0f7e8', 'Forest'],
                    ['#4a1942', '#fce4f7', 'Plum'],
                    ['#7c2d12', '#fef3e2', 'Amber'],
                    ['#0f172a', '#f8fafc', 'Dark Pro'],
                    ['#ffffff', '#1e1b4b', 'Inverted'],
                    ['#be185d', '#fdf2f8', 'Rose'],
                    ] as [$fg, $bg, $name])
                    <button
                        wire:click="$set('fgColor','{{ $fg }}'); $wire.bgColor = '{{ $bg }}';"
                        class="flex items-center gap-2 px-3 py-1.5 rounded-lg border border-zinc-200 dark:border-zinc-700 hover:border-violet-400 transition-all text-xs font-medium text-zinc-600 dark:text-zinc-300 bg-zinc-50 dark:bg-zinc-800">
                        <span class="flex gap-1">
                            <span class="inline-block w-3.5 h-3.5 rounded-full border border-zinc-300" style="background:{{ $fg }};"></span>
                            <span class="inline-block w-3.5 h-3.5 rounded-full border border-zinc-300" style="background:{{ $bg }};"></span>
                        </span>
                        {{ $name }}
                    </button>
                    @endforeach
                </div>
            </div>

            {{-- ── Actions ── --}}
            <div class="flex gap-3">
                <flux:button
                    wire:click="generate"
                    wire:loading.attr="disabled"
                    variant="primary"
                    icon="qr-code"
                    class="flex-1 bg-violet-600 hover:bg-violet-700 text-white border-violet-600!">
                    <span wire:loading.remove wire:target="generate">Generate QR Code</span>
                    <span wire:loading wire:target="generate" class="flex items-center gap-2">
                        <flux:icon icon="loading" class="size-4 animate-spin" />
                        Generating…
                    </span>
                </flux:button>
            </div>

            {{-- Validation errors --}}
            @if($errors->any())
            <div class="rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 p-4 text-sm text-red-700 dark:text-red-400 space-y-1">
                @foreach($errors->all() as $error)
                <p class="flex items-start gap-2">
                    <flux:icon icon="exclamation-circle" class="size-4 mt-0.5 shrink-0" />{{ $error }}
                </p>
                @endforeach
            </div>
            @endif
        </div>

        {{-- ──────────────────────── Right Panel — Preview ───────────────── --}}
        <div class="xl:col-span-2">
            <div class="sticky top-6 space-y-4">
                <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800/80 shadow-sm p-5 space-y-4">
                    <div class="flex items-center gap-2 mb-1">
                        <div class="p-1.5 rounded-lg bg-violet-50 dark:bg-violet-900/30">
                            <flux:icon icon="eye" class="size-3.5 text-violet-600 dark:text-violet-400" />
                        </div>
                        <p class="text-xs font-bold text-zinc-700 dark:text-zinc-200 uppercase tracking-widest">
                            Preview
                        </p>
                    </div>

                    {{-- QR Preview area --}}
                    <div
                        class="relative flex items-center justify-center rounded-2xl overflow-hidden"
                        style="min-height: 280px; ">
                        @if($qrDataUri)
                        <div class="relative group">
                            <img
                                src="{{ $qrDataUri }}"
                                alt="Generated QR Code"
                                class="block max-w-full"
                                style="max-height: 320px;" />
                            {{-- Hover overlay --}}
                            <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-200 bg-black/10 rounded-lg">
                                <p class="text-white text-xs font-semibold bg-black/60 rounded px-2 py-1">Click to download ↓</p>
                            </div>
                        </div>
                        @else
                        <div class="flex flex-col items-center gap-3 py-16 text-zinc-400">
                            <div class="p-4">
                                <flux:icon icon="qr-code" class="size-12 text-zinc-400" />
                            </div>
                            <p class="text-sm">Your QR code will appear here</p>
                        </div>
                        @endif

                        {{-- Loading overlay --}}
                        <div wire:loading wire:target="generate" class="absolute inset-0 flex items-center justify-center bg-white/80 dark:bg-zinc-900/80 rounded-2xl backdrop-blur-sm">
                            <div class="flex flex-col items-center  gap-3">
                                <flux:icon icon="loading" class="size-10 text-violet-500 animate-spin" />
                                <p class="text-sm text-violet-600 dark:text-violet-400 font-medium">Generating…</p>
                            </div>
                        </div>
                    </div>

                    {{-- Flash message --}}
                    @if($flashMsg)
                    <div class="mt-3 px-4 py-2 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg text-sm text-green-700 dark:text-green-400 flex items-center gap-2">
                        <flux:icon icon="check-circle" class="size-4" />
                        {{ $flashMsg }}
                    </div>
                    @endif

                    {{-- Download / Copy actions --}}
                    @if($qrDataUri)
                    <div class="mt-4 space-y-2">
                        <a
                            href="{{ $qrDataUri }}"
                            download="qrcode.{{ $format }}"
                            class="flex items-center justify-center gap-2 w-full px-4 py-2.5 bg-violet-600 hover:bg-violet-700 text-white text-sm font-semibold rounded-xl transition-colors">
                            <flux:icon icon="arrow-down-tray" class="size-4" />
                            Download {{ strtoupper($format) }}
                        </a>
                    </div>
                    @endif
                </div>

                {{-- Info card --}}
                @if($qrDataUri)
                <div class="bg-zinc-50 dark:bg-zinc-800/50 rounded-2xl border border-zinc-200/80 dark:border-zinc-700/50 shadow-sm p-4 text-xs text-zinc-500 dark:text-zinc-400 space-y-1">
                    <p><span class="font-semibold text-zinc-700 dark:text-zinc-300">Format:</span> {{ strtoupper($format) }}</p>
                    <p><span class="font-semibold text-zinc-700 dark:text-zinc-300">Size:</span> {{ $size }}×{{ $size }} px</p>
                    <p><span class="font-semibold text-zinc-700 dark:text-zinc-300">Margin:</span> {{ $margin }} px</p>
                    <p><span class="font-semibold text-zinc-700 dark:text-zinc-300">Data length:</span> {{ mb_strlen($data) }} chars</p>
                </div>
                @endif
            </div>
        </div>

    </div>

</div>