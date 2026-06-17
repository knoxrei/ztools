<?php

use App\Http\Controllers\FakeIdentity;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Fake Identity Generator')] class extends Component
{
    public string $region      = 'global';
    public array  $identity    = [];
    public string $activeTab   = 'personal';

    public function mount(): void
    {
        $this->generateIdentity();
    }

    public function generateIdentity(): void
    {
        $this->identity  = (new FakeIdentity())->generate($this->region);
        $this->activeTab = 'personal';
    }

    public function updatedRegion(): void
    {
        $this->generateIdentity();
    }

    public function exportJson(): void
    {
        $json = json_encode($this->identity, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
        $this->dispatch('download-json', content: $json, filename: 'fake-identity-' . now()->format('YmdHis') . '.json');
    }
};
?>

<div
    x-data="{
        copiedSection: null,
        copiedField:   null,
        toastMsg:      null,
        toastTimer:    null,

        copyField(section, field, label) {
            const val = ($wire.identity?.[section]?.[field] ?? '');
            if (!val && val !== 0) return;
            const text = String(val);
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(text).then(() => {
                    this.copiedSection = section;
                    this.copiedField   = field;
                    this.showToast(label + ' copied!');
                    setTimeout(() => {
                        this.copiedSection = null;
                        this.copiedField   = null;
                    }, 1800);
                });
            } else {
                const textArea = document.createElement('textarea');
                textArea.value = text;
                textArea.style.position = 'fixed';
                textArea.style.opacity = '0';
                document.body.appendChild(textArea);
                textArea.focus();
                textArea.select();
                try {
                    document.execCommand('copy');
                    this.copiedSection = section;
                    this.copiedField   = field;
                    this.showToast(label + ' copied!');
                    setTimeout(() => {
                        this.copiedSection = null;
                        this.copiedField   = null;
                    }, 1800);
                } catch (err) {
                    console.error('Fallback copy failed', err);
                }
                document.body.removeChild(textArea);
            }
        },

        showToast(msg) {
            clearTimeout(this.toastTimer);
            this.toastMsg = msg;
            this.toastTimer = setTimeout(() => this.toastMsg = null, 2200);
        }
    }"
    x-on:download-json.window="
        const a = document.createElement('a');
        a.href = 'data:application/json;charset=utf-8,' + encodeURIComponent($event.detail.content);
        a.download = $event.detail.filename;
        a.click();
    "
    class="min-h-screen pb-12">

    {{-- ─────────────────── Toast ────────────────────── --}}
    <div
        x-show="toastMsg"
        class="fixed bottom-6 right-6 z-50 flex items-center gap-2 px-4 py-2.5 bg-zinc-900 dark:bg-zinc-100 text-white dark:text-zinc-900 text-sm font-semibold rounded-xl shadow-2xl border border-zinc-700/30 dark:border-zinc-300/30"
        style="display:none">
        <flux:icon icon="check-circle" class="size-4 text-emerald-400 dark:text-emerald-600 shrink-0" />
        <span x-text="toastMsg"></span>
    </div>

    {{-- ─────────────────── Page Header ─────────────────── --}}
    <div class="mb-8">
        <div class="flex items-center gap-3 mb-2">
            <div class="p-2  rounded-xl">
                <flux:icon icon="identification" class="size-7 text-violet-600 dark:text-violet-400" />
            </div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white tracking-tight">Fake Identity Generator</h1>
        </div>
        <p class="text-sm text-zinc-500 dark:text-zinc-400 ml-14">
            Generate fake identities.
        </p>
    </div>

    {{-- ─────────────────── Controls ─────────────────── --}}
    <div class="flex flex-wrap items-center gap-2 mb-6">

        {{-- Region selector --}}
        <div class="flex items-center gap-1 p-1 bg-zinc-100 dark:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-700">
            @foreach([
            ['global', 'globe-alt', 'Global'],
            ['asian', 'map-pin', 'Asian'],
            ['european', 'building-library', 'European'],
            ] as [$key, $icon, $label])
            <button
                wire:click="$set('region', '{{ $key }}')"
                wire:loading.attr="disabled"
                @class([ 'flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium' , 'bg-white dark:bg-zinc-700 text-violet-600 dark:text-violet-400 shadow-sm font-bold'=> $region === $key,
                'text-zinc-500 dark:text-zinc-400 hover:text-zinc-800 dark:hover:text-zinc-200' => $region !== $key,
                ])>
                <flux:icon icon="{{ $icon }}" class="size-3.5" />
                {{ $label }}
            </button>
            @endforeach
        </div>

        {{-- Locale tag --}}
        @if(!empty($identity['meta']['locale']))
        <span class="px-2.5 py-1.5 rounded-lg bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-xs font-mono text-zinc-500 dark:text-zinc-400">
            {{ $identity['meta']['locale'] }}
        </span>
        @endif

        <div class="ml-auto flex items-center gap-2 flex-wrap">
            {{-- Export --}}
            <flux:button wire:click="exportJson" variant="ghost" size="sm" icon="arrow-down-tray">
                Export
            </flux:button>

            {{-- Generate --}}
            <flux:button
                wire:click="generateIdentity"
                wire:loading.attr="disabled"
                variant="primary"
                icon="arrow-path"
                class="bg-violet-600 hover:bg-violet-700 border-violet-600! text-white">
                <span wire:loading.remove wire:target="generateIdentity">Generate New</span>
                <span wire:loading wire:target="generateIdentity" class="flex items-center gap-2">
                    <flux:icon icon="loading" class="size-4 animate-spin" />
                    Generating…
                </span>
            </flux:button>
        </div>
    </div>

    @if(!empty($identity))

    {{-- ─────────────────── ID Card ─────────────────── --}}
    <div class="mb-7 rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900 shadow-sm p-5 relative font-sans overflow-hidden">

        {{-- Card Header Stripe --}}
        <div class="flex items-center justify-between border-b border-zinc-200 dark:border-zinc-800 pb-3 mb-4">
            <div class="flex items-center gap-2">
                <span class="px-2 py-0.5 rounded bg-zinc-200 dark:bg-zinc-800 text-[9px] font-bold text-zinc-600 dark:text-zinc-400">
                    {{ $identity['address']['country_code'] ?? 'ID' }}
                </span>
                <span class="text-[10px] font-extrabold uppercase tracking-widest text-zinc-500 dark:text-zinc-400">
                    Republic of {{ $identity['address']['country'] ?? 'Global' }}
                </span>
            </div>
            <span class="text-[9px] font-bold font-mono px-2 py-0.5 rounded border border-zinc-300 dark:border-zinc-700 text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                Identity Document
            </span>
        </div>

        {{-- Content Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-start">

            {{-- Left Column: Avatar & Signature --}}
            <div class="col-span-1 md:col-span-3 flex flex-col items-center">
                <div class="relative w-28 h-36 rounded-xl bg-zinc-200 dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 flex items-center justify-center shadow-inner overflow-hidden">
                    @if(!empty($identity['personal']['avatar']))
                    <img src="{{ $identity['personal']['avatar'] }}" alt="Avatar" class="w-full h-full object-cover" />
                    @else
                    <flux:icon icon="user-circle" class="size-16 text-zinc-500 dark:text-zinc-400" />
                    @endif

                    {{-- Gender dot indicator --}}

                </div>

                {{-- Stylized Signature --}}
                <div class="mt-3 w-full text-center border-t border-dashed border-zinc-300 dark:border-zinc-700 pt-1.5 px-2">
                    <span class="text-[11px] font-mono text-zinc-400 dark:text-zinc-500 italic font-bold">
                        {{ $identity['personal']['first_name'] ?? '' }} {{ $identity['personal']['last_name'] ?? '' }}
                    </span>
                    <p class="text-[7px] uppercase tracking-widest text-zinc-400 dark:text-zinc-500 mt-0.5">Signature</p>
                </div>
            </div>

            {{-- Middle Column: Core Personal Details --}}
            <div class="col-span-1 md:col-span-6 grid grid-cols-1 gap-y-3">

                <div>
                    <p class="text-[8px] uppercase tracking-wider text-zinc-400 dark:text-zinc-500 leading-none">Full Name</p>
                    <p class="text-sm font-extrabold text-zinc-800 dark:text-zinc-100 uppercase tracking-tight">
                        {{ $identity['personal']['full_name'] ?? '' }}
                    </p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-[8px] uppercase tracking-wider text-zinc-400 dark:text-zinc-500 leading-none">Date of Birth</p>
                        <p class="text-xs font-bold text-zinc-700 dark:text-zinc-300 font-mono">
                            {{ $identity['personal']['date_of_birth'] ?? '' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-[8px] uppercase tracking-wider text-zinc-400 dark:text-zinc-500 leading-none">Nationality</p>
                        <p class="text-xs font-semibold text-zinc-700 dark:text-zinc-300">
                            {{ $identity['personal']['nationality'] ?? '' }}
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-2">
                    <div>
                        <p class="text-[8px] uppercase tracking-wider text-zinc-400 dark:text-zinc-500 leading-none">Sex</p>
                        <p class="text-xs font-semibold text-zinc-700 dark:text-zinc-300">
                            {{ substr($identity['personal']['gender'] ?? 'N', 0, 1) }}
                        </p>
                    </div>
                    <div>
                        <p class="text-[8px] uppercase tracking-wider text-zinc-400 dark:text-zinc-500 leading-none">Height</p>
                        <p class="text-xs font-semibold text-zinc-700 dark:text-zinc-300 font-mono">
                            {{ $identity['personal']['height'] ?? '' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-[8px] uppercase tracking-wider text-zinc-400 dark:text-zinc-500 leading-none">Blood Type</p>
                        <p class="text-xs font-semibold text-zinc-700 dark:text-zinc-300">
                            {{ $identity['personal']['blood_type'] ?? '' }}
                        </p>
                    </div>
                </div>

                <div>
                    <p class="text-[8px] uppercase tracking-wider text-zinc-400 dark:text-zinc-500 leading-none">Address</p>
                    <p class="text-xs font-medium text-zinc-700 dark:text-zinc-300 leading-snug">
                        {{ $identity['address']['street'] ?? '' }}, {{ $identity['address']['city'] ?? '' }}, {{ $identity['address']['zip'] ?? '' }}
                    </p>
                </div>
            </div>

            {{-- Right Column: ID numbers, Issuer, Barcode --}}
            <div class="col-span-1 md:col-span-3 flex flex-col justify-between h-full md:items-end text-left md:text-right gap-y-4">

                <div>
                    <p class="text-[8px] uppercase tracking-wider text-zinc-400 dark:text-zinc-500 leading-none mb-1">Document Number</p>
                    <p class="text-xs font-bold text-red-600 dark:text-red-400 font-mono leading-none tracking-wide">
                        {{ $identity['personal']['ssn_or_id'] ?? '' }}
                    </p>
                </div>

                <div>
                    <p class="text-[8px] uppercase tracking-wider text-zinc-400 dark:text-zinc-500 leading-none mb-1">Passport Number</p>
                    <p class="text-xs font-bold text-zinc-700 dark:text-zinc-300 font-mono leading-none">
                        {{ $identity['personal']['passport_no'] ?? '' }}
                    </p>
                </div>

                <div>
                    <p class="text-[8px] uppercase tracking-wider text-zinc-400 dark:text-zinc-500 leading-none mb-1">Driver Lic. Number</p>
                    <p class="text-xs font-bold text-zinc-700 dark:text-zinc-300 font-mono leading-none">
                        {{ $identity['personal']['drivers_license'] ?? '' }}
                    </p>
                </div>

                {{-- Stylized Digital Barcode --}}
                <div class="flex flex-col md:items-end mt-1">
                    <div class="flex items-center gap-[1px] h-7 bg-zinc-800 dark:bg-zinc-200 px-1.5 py-0.5 rounded-sm">
                        <div class="w-[1px] h-full bg-zinc-100 dark:bg-zinc-900"></div>
                        <div class="w-[3px] h-full bg-zinc-100 dark:bg-zinc-900"></div>
                        <div class="w-[1px] h-full bg-zinc-100 dark:bg-zinc-900"></div>
                        <div class="w-[2px] h-full bg-zinc-100 dark:bg-zinc-900"></div>
                        <div class="w-[1px] h-full bg-zinc-100 dark:bg-zinc-900"></div>
                        <div class="w-[4px] h-full bg-zinc-100 dark:bg-zinc-900"></div>
                        <div class="w-[1px] h-full bg-zinc-100 dark:bg-zinc-900"></div>
                        <div class="w-[2px] h-full bg-zinc-100 dark:bg-zinc-900"></div>
                        <div class="w-[3px] h-full bg-zinc-100 dark:bg-zinc-900"></div>
                        <div class="w-[1px] h-full bg-zinc-100 dark:bg-zinc-900"></div>
                        <div class="w-[2px] h-full bg-zinc-100 dark:bg-zinc-900"></div>
                    </div>
                    <span class="text-[6px] font-mono text-zinc-400 dark:text-zinc-500 mt-1 tracking-widest uppercase">
                        SECURE IDENTITY DOC
                    </span>
                </div>
            </div>

        </div>
    </div>

    {{-- ─────────────────── Tab Navigation ─────────────────── --}}
    @php
    $tabs = [
    ['personal', 'identification', 'Personal'],
    ['contact', 'envelope', 'Contact'],
    ['address', 'map-pin', 'Address'],
    ['financial', 'credit-card', 'Financial'],
    ['internet', 'wifi', 'Internet'],
    ['employment', 'briefcase', 'Employment'],
    ['vehicle', 'truck', 'Vehicle'],
    ['medical', 'heart', 'Medical'],
    ];
    @endphp

    <div class="mb-5 overflow-x-auto">
        <div class="flex gap-1 p-1.5 bg-zinc-100 dark:bg-zinc-800/80 rounded-2xl border border-zinc-200 dark:border-zinc-700/60 w-max min-w-full">
            @foreach($tabs as [$key, $icon, $label])
            <button
                wire:click="$set('activeTab', '{{ $key }}')"
                @class([ 'flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-sm font-semibold whitespace-nowrap' , 'bg-white dark:bg-zinc-700 text-violet-600 dark:text-violet-400 shadow-sm font-bold'=> $activeTab === $key,
                'text-zinc-500 dark:text-zinc-400 hover:text-zinc-800 dark:hover:text-zinc-200' => $activeTab !== $key,
                ])>
                <flux:icon icon="{{ $icon }}" class="size-3.5" />
                {{ $label }}
            </button>
            @endforeach
        </div>
    </div>

    {{-- ─────────────────── Tab Content ─────────────────── --}}
    <div wire:key="tab-{{ $activeTab }}" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">

        {{-- ── PERSONAL ── --}}
        @if($activeTab === 'personal')
        <x-fake-id-card title="Identity" icon="identification" section="personal">
            <x-fake-id-field section="personal" field="full_name" label="Full Name" icon="user" />
            <x-fake-id-field section="personal" field="first_name" label="First Name" icon="user" />
            <x-fake-id-field section="personal" field="last_name" label="Last Name" icon="user" />
            <x-fake-id-field section="personal" field="username" label="Username" icon="at-symbol" :mono="true" />
            <x-fake-id-field section="personal" field="gender" label="Gender" icon="user-circle" :badge="true" />
            <x-fake-id-field section="personal" field="age" label="Age" icon="calendar" />
            <x-fake-id-field section="personal" field="date_of_birth" label="Date of Birth" icon="calendar" :mono="true" />
            <x-fake-id-field section="personal" field="nationality" label="Nationality" icon="flag" />
        </x-fake-id-card>

        <x-fake-id-card title="Background" icon="academic-cap" section="personal">
            <x-fake-id-field section="personal" field="marital_status" label="Marital Status" icon="heart" :badge="true" />
            <x-fake-id-field section="personal" field="education" label="Education" icon="academic-cap" />
            <x-fake-id-field section="personal" field="religion" label="Religion" icon="sparkles" />
            <x-fake-id-field section="personal" field="zodiac" label="Zodiac" icon="star" :badge="true" />
            <x-fake-id-field section="personal" field="mothers_maiden" label="Mother's Maiden" icon="user-group" />
        </x-fake-id-card>

        <x-fake-id-card title="Documents & Physical" icon="lock-closed" section="personal">
            <x-fake-id-field section="personal" field="ssn_or_id" label="SSN / NIK" icon="lock-closed" :mono="true" />
            <x-fake-id-field section="personal" field="passport_no" label="Passport" icon="identification" :mono="true" />
            <x-fake-id-field section="personal" field="drivers_license" label="Driver Lic." icon="truck" :mono="true" />
            <x-fake-id-field section="personal" field="blood_type" label="Blood Type" icon="heart" :badge="true" />
            <x-fake-id-field section="personal" field="height" label="Height" icon="arrows-up-down" />
            <x-fake-id-field section="personal" field="weight" label="Weight" icon="scale" />
            <x-fake-id-field section="personal" field="eye_color" label="Eye Color" icon="eye" :badge="true" />
            <x-fake-id-field section="personal" field="hair_color" label="Hair Color" icon="sparkles" :badge="true" />
            <x-fake-id-field section="personal" field="hair_length" label="Hair Length" icon="scissors" :badge="true" />
        </x-fake-id-card>

        {{-- ── CONTACT ── --}}
        @elseif($activeTab === 'contact')
        <x-fake-id-card title="Email & Phone" icon="envelope" section="contact">
            <x-fake-id-field section="contact" field="email" label="Email" icon="envelope" />
            <x-fake-id-field section="contact" field="email_alt" label="Alt Email" icon="envelope" />
            <x-fake-id-field section="contact" field="phone" label="Phone" icon="phone" :mono="true" />
            <x-fake-id-field section="contact" field="phone_mobile" label="Mobile" icon="device-phone-mobile" :mono="true" />
        </x-fake-id-card>

        <x-fake-id-card title="Online Presence" icon="globe-alt" section="contact">
            <x-fake-id-field section="contact" field="website" label="Website" icon="globe-alt" />
            <x-fake-id-field section="contact" field="linkedin" label="LinkedIn" icon="link" />
            <x-fake-id-field section="contact" field="twitter" label="Twitter/X" icon="at-symbol" :mono="true" />
            <x-fake-id-field section="contact" field="github" label="GitHub" icon="code-bracket" />
        </x-fake-id-card>

        {{-- ── ADDRESS ── --}}
        @elseif($activeTab === 'address')
        <x-fake-id-card title="Street Address" icon="home" section="address">
            <x-fake-id-field section="address" field="street" label="Street" icon="home" />
            <x-fake-id-field section="address" field="city" label="City" icon="building-office" />
            <x-fake-id-field section="address" field="state" label="State / Prov." icon="map" />
            <x-fake-id-field section="address" field="zip" label="ZIP / Post" icon="hashtag" :mono="true" />
            <x-fake-id-field section="address" field="country" label="Country" icon="flag" />
            <x-fake-id-field section="address" field="country_code" label="ISO Code" icon="identification" :mono="true" :badge="true" />
        </x-fake-id-card>

        <x-fake-id-card title="Geolocation" icon="map-pin" section="address">
            <x-fake-id-field section="address" field="latitude" label="Latitude" icon="arrow-up" :mono="true" />
            <x-fake-id-field section="address" field="longitude" label="Longitude" icon="arrow-right" :mono="true" />
            <x-fake-id-field section="address" field="timezone" label="Timezone" icon="clock" />
        </x-fake-id-card>

        {{-- ── FINANCIAL ── --}}
        @elseif($activeTab === 'financial')
        <x-fake-id-card title="Credit Card" icon="credit-card" section="financial">
            <x-fake-id-field section="financial" field="card_brand" label="Brand" icon="credit-card" :badge="true" />
            <x-fake-id-field section="financial" field="card_number" label="Card Number" icon="hashtag" :mono="true" />
            <x-fake-id-field section="financial" field="card_expiry" label="Expires" icon="calendar" :mono="true" />
            <x-fake-id-field section="financial" field="card_cvv" label="CVV" icon="lock-closed" :mono="true" />
            <x-fake-id-field section="financial" field="card_pin" label="PIN" icon="key" :mono="true" />
        </x-fake-id-card>

        <x-fake-id-card title="Banking" icon="building-library" section="financial">
            <x-fake-id-field section="financial" field="iban" label="IBAN" icon="building-library" :mono="true" />
            <x-fake-id-field section="financial" field="swift_bic" label="SWIFT/BIC" icon="globe-alt" :mono="true" />
            <x-fake-id-field section="financial" field="currency" label="Currency" icon="banknotes" :badge="true" />
        </x-fake-id-card>

        <x-fake-id-card title="Crypto & Wealth" icon="currency-dollar" section="financial">
            <x-fake-id-field section="financial" field="bitcoin" label="Bitcoin" icon="currency-dollar" :mono="true" />
            <x-fake-id-field section="financial" field="ethereum" label="Ethereum" icon="currency-dollar" :mono="true" />
            <x-fake-id-field section="financial" field="salary" label="Salary" icon="banknotes" />
            <x-fake-id-field section="financial" field="net_worth" label="Net Worth" icon="chart-bar" />
        </x-fake-id-card>

        {{-- ── INTERNET ── --}}
        @elseif($activeTab === 'internet')
        <x-fake-id-card title="Network" icon="globe-alt" section="internet">
            <x-fake-id-field section="internet" field="ipv4" label="IPv4 Address" icon="globe-alt" :mono="true" />
            <x-fake-id-field section="internet" field="ipv6" label="IPv6 Address" icon="globe-alt" :mono="true" />
            <x-fake-id-field section="internet" field="mac_address" label="MAC Address" icon="cpu-chip" :mono="true" />
            <x-fake-id-field section="internet" field="isp" label="ISP" icon="building-office-2" />
            <x-fake-id-field section="internet" field="connection" label="Connection" icon="signal" :badge="true" />
            <x-fake-id-field section="internet" field="speed" label="Speed" icon="bolt" />
        </x-fake-id-card>

        <x-fake-id-card title="Browser & OS" icon="computer-desktop" section="internet">
            <x-fake-id-field section="internet" field="browser" label="Browser" icon="window" :badge="true" />
            <x-fake-id-field section="internet" field="browser_ver" label="Version" icon="tag" :mono="true" />
            <x-fake-id-field section="internet" field="os" label="OS" icon="computer-desktop" />
            <x-fake-id-field section="internet" field="domain" label="Domain" icon="globe-alt" :mono="true" />
            <x-fake-id-field section="internet" field="tld" label="TLD" icon="hashtag" :mono="true" :badge="true" />
        </x-fake-id-card>

        {{-- User Agent full string --}}
        <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200/80 dark:border-zinc-700/50 shadow-sm p-5 md:col-span-2 xl:col-span-1">
            <div class="flex items-center gap-2 mb-3">
                <div class="p-1.5 rounded-lg bg-violet-50 dark:bg-violet-900/30">
                    <flux:icon icon="code-bracket" class="size-3.5 text-violet-600 dark:text-violet-400" />
                </div>
                <p class="text-xs font-bold text-zinc-700 dark:text-zinc-200 uppercase tracking-widest">User Agent</p>
            </div>
            <p class="text-[11px] font-mono text-zinc-500 dark:text-zinc-400 break-all leading-relaxed bg-zinc-50 dark:bg-zinc-800 rounded-xl p-3 border border-zinc-200 dark:border-zinc-700"
                x-text="$wire.identity?.internet?.user_agent ?? '–'"></p>
            <button
                x-on:click="copyField('internet', 'user_agent', 'User Agent')"
                class="mt-2 flex items-center gap-1.5 text-xs text-violet-500 hover:text-violet-700 font-semibold">
                <flux:icon icon="clipboard" class="size-3.5" />
                Copy UA string
            </button>
        </div>

        {{-- ── EMPLOYMENT ── --}}
        @elseif($activeTab === 'employment')
        <x-fake-id-card title="Position" icon="briefcase" section="employment">
            <x-fake-id-field section="employment" field="company" label="Company" icon="building-office-2" />
            <x-fake-id-field section="employment" field="job_title" label="Job Title" icon="briefcase" />
            <x-fake-id-field section="employment" field="department" label="Department" icon="squares-2x2" :badge="true" />
            <x-fake-id-field section="employment" field="salary" label="Salary" icon="banknotes" />
            <x-fake-id-field section="employment" field="start_date" label="Start Date" icon="calendar" :mono="true" />
        </x-fake-id-card>

        <x-fake-id-card title="Work Details" icon="identification" section="employment">
            <x-fake-id-field section="employment" field="company_email" label="Work Email" icon="envelope" />
            <x-fake-id-field section="employment" field="work_phone" label="Work Phone" icon="phone" :mono="true" />
            <x-fake-id-field section="employment" field="employee_id" label="Employee ID" icon="identification" :mono="true" :badge="true" />
        </x-fake-id-card>

        {{-- ── VEHICLE ── --}}
        @elseif($activeTab === 'vehicle')
        <x-fake-id-card title="Vehicle Info" icon="truck" section="vehicle">
            <x-fake-id-field section="vehicle" field="brand" label="Brand" icon="truck" />
            <x-fake-id-field section="vehicle" field="model" label="Model" icon="tag" />
            <x-fake-id-field section="vehicle" field="year" label="Year" icon="calendar" :badge="true" />
            <x-fake-id-field section="vehicle" field="color" label="Color" icon="swatch" :badge="true" />
            <x-fake-id-field section="vehicle" field="type" label="Type" icon="squares-2x2" :badge="true" />
            <x-fake-id-field section="vehicle" field="fuel" label="Fuel Type" icon="beaker" :badge="true" />
        </x-fake-id-card>

        <x-fake-id-card title="Registration" icon="identification" section="vehicle">
            <x-fake-id-field section="vehicle" field="plate" label="Plate No." icon="hashtag" :mono="true" :badge="true" />
            <x-fake-id-field section="vehicle" field="vin" label="VIN" icon="identification" :mono="true" :secret="true" />
            <x-fake-id-field section="vehicle" field="insurance" label="Insurance" icon="shield-check" :mono="true" :secret="true" />
        </x-fake-id-card>

        {{-- ── MEDICAL ── --}}
        @elseif($activeTab === 'medical')
        <x-fake-id-card title="Health Info" icon="heart" section="medical">
            <x-fake-id-field section="medical" field="blood_type" label="Blood Type" icon="heart" :badge="true" />
            <x-fake-id-field section="medical" field="allergies" label="Allergies" icon="exclamation-circle" />
            <x-fake-id-field section="medical" field="conditions" label="Conditions" icon="clipboard" />
            <x-fake-id-field section="medical" field="medications" label="Medications" icon="beaker" />
            <x-fake-id-field section="medical" field="organ_donor" label="Organ Donor" icon="sparkles" :badge="true" />
        </x-fake-id-card>

        <x-fake-id-card title="Healthcare" icon="shield-check" section="medical">
            <x-fake-id-field section="medical" field="doctor" label="Primary Doctor" icon="user" />
            <x-fake-id-field section="medical" field="insurance_id" label="Health Ins. ID" icon="shield-check" :mono="true" :secret="true" />
        </x-fake-id-card>
        @endif

    </div>

    {{-- ─────────────────── Footer ─────────────────── --}}
    <div class="mt-6 flex items-center justify-between text-[10px] text-zinc-400 dark:text-zinc-600 font-mono">
        <span>Locale: {{ $identity['meta']['locale'] ?? '' }} · Region: {{ ucfirst($identity['meta']['region'] ?? '') }}</span>
        <span>Generated: {{ $identity['meta']['generated'] ?? '' }}</span>
    </div>

    @endif

</div>