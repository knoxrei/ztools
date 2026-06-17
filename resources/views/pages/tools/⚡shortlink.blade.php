<?php

use Livewire\Component;
use Livewire\Attributes\Title;
use Illuminate\Support\Str;
use App\Models\ShortLink;
use App\Http\Controllers\QrGenerator;

new #[Title('URL Shortener & Cloaker')] class extends Component
{
    public string $original_url = '';
    public string $custom_code = '';
    public string $password = '';
    public string $expiry = 'never'; // never, 1h, 24h, 7d, 30d
    public string $max_clicks = '';
    public bool $is_burn_after_use = false;
    public string $cloak_title = '';
    public string $cloak_desc = '';
    public string $connection_type = 'both'; // clearnet, tor, both

    // Settings visibility toggles
    public bool $showAdvanced = false;
    public bool $enableCloaking = false;

    // Output
    public ?string $shortened_url = null;
    public ?string $qr_code_uri = null;
    public ?array $new_link_details = null;

    protected array $rules = [
        'original_url' => 'required|url|max:2048',
        'custom_code' => 'nullable|alpha_dash|max:50',
        'password' => 'nullable|string|min:4|max:100',
        'max_clicks' => 'nullable|integer|min:1|max:1000000',
        'cloak_title' => 'nullable|string|max:100',
        'cloak_desc' => 'nullable|string|max:500',
        'connection_type' => 'required|in:clearnet,tor,both',
        'showAdvanced' => 'boolean',
        'enableCloaking' => 'boolean',
    ];

    public function shorten()
    {
        $this->validate();

        // Sanitize values if toggles are off
        if (!$this->showAdvanced) {
            $this->expiry = 'never';
            $this->max_clicks = '';
            $this->password = '';
            $this->is_burn_after_use = false;
            $this->enableCloaking = false;
            $this->connection_type = 'both';
        }

        if (!$this->enableCloaking) {
            $this->cloak_title = '';
            $this->cloak_desc = '';
        }

        // 1. Generate or validate code
        $code = trim($this->custom_code);
        if ($code === '') {
            // Generate random string
            do {
                $code = Str::random(6);
            } while (ShortLink::where('code', $code)->exists());
        } else {
            // Check uniqueness
            if (ShortLink::where('code', $code)->exists()) {
                $this->addError('custom_code', 'This custom alias is already taken.');
                return;
            }
        }

        // 2. Parse expiry
        $expiresAt = null;
        if ($this->expiry !== 'never') {
            $expiresAt = match ($this->expiry) {
                '1h' => now()->addHour(),
                '24h' => now()->addDay(),
                '7d' => now()->addDays(7),
                '30d' => now()->addDays(30),
                default => null,
            };
        }

        // 3. Hash password if provided
        $hashedPassword = null;
        if ($this->password !== '') {
            $hashedPassword = bcrypt($this->password);
        }

        // 4. Create ShortLink
        $shortLink = ShortLink::create([
            'code' => $code,
            'original_url' => $this->original_url,
            'password' => $hashedPassword,
            'expires_at' => $expiresAt,
            'max_clicks' => $this->max_clicks !== '' ? (int) $this->max_clicks : null,
            'is_burn_after_use' => $this->is_burn_after_use,
            'cloak_title' => $this->cloak_title !== '' ? $this->cloak_title : null,
            'cloak_desc' => $this->cloak_desc !== '' ? $this->cloak_desc : null,
            'connection_type' => $this->connection_type,
        ]);

        // Save to session history
        session()->push('created_short_links', $code);

        // Fetch connection bases from .env
        $clearnetBase = rtrim(env('CLEARNET_CONNECTION', 'http://10.134.142.153:8000'), '/');
        $torBase = rtrim(env('TOR_CONNECTION', 'http://zknoxonionabcde.onion'), '/');
        
        $clearnetUrl = $clearnetBase . '/s/' . $code;
        $torUrl = $torBase . '/s/' . $code;

        // Check if visitor is currently on Tor network domain (.onion)
        $isClientOnTor = Str::endsWith(request()->getHost(), '.onion') || request()->header('X-Tor-Onion') || env('APP_ENV') === 'tor';

        // Set returned link address
        if ($this->connection_type === 'clearnet') {
            $this->shortened_url = $clearnetUrl;
        } elseif ($this->connection_type === 'tor') {
            $this->shortened_url = $torUrl;
        } else { // both
            $this->shortened_url = $isClientOnTor ? $torUrl : $clearnetUrl;
        }

        // Generate QR code data URI
        try {
            $qrGenerator = new QrGenerator();
            $this->qr_code_uri = $qrGenerator->generateDataUri($this->shortened_url, size: 200, margin: 5);
        } catch (\Throwable $e) {
            $qr_code_uri = null;
        }

        $this->new_link_details = [
            'code' => $code,
            'original_url' => $this->original_url,
            'has_password' => !empty($hashedPassword),
            'expires_at' => $expiresAt ? $expiresAt->toDateTimeString() : 'Never',
            'max_clicks' => $this->max_clicks !== '' ? $this->max_clicks : 'Unlimited',
            'is_burn_after_use' => $this->is_burn_after_use,
            'cloak_title' => $this->cloak_title ?: 'None',
            'connection_type' => $this->connection_type,
            'clearnet_url' => $clearnetUrl,
            'tor_url' => $torUrl,
        ];

        // Reset input fields but keep original_url
        $this->reset([
            'custom_code',
            'password',
            'expiry',
            'max_clicks',
            'is_burn_after_use',
            'cloak_title',
            'cloak_desc',
            'showAdvanced',
            'enableCloaking',
            'connection_type'
        ]);
    }

    public function deleteLink($code)
    {
        $link = ShortLink::where('code', $code)->first();
        if ($link) {
            $link->delete();
        }

        // Remove from session
        $codes = session('created_short_links', []);
        if (($key = array_search($code, $codes)) !== false) {
            unset($codes[$key]);
            session(['created_short_links' => array_values($codes)]);
        }
    }

    public function clearResults()
    {
        $this->reset(['shortened_url', 'qr_code_uri', 'new_link_details']);
    }

    public function getHistory()
    {
        $codes = session('created_short_links', []);
        if (empty($codes)) {
            return [];
        }
        return ShortLink::whereIn('code', $codes)->orderBy('created_at', 'desc')->get();
    }
};
?>

<div class="min-h-screen pb-12">

    {{-- Page Header --}}
    <div class="mb-8">
        <div class="flex items-center gap-3 mb-2">
            <div class="p-2">
                <flux:icon icon="link" class="size-7 text-violet-600 dark:text-violet-400" />
            </div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white tracking-tight">URL Shortener & Cloaker</h1>
        </div>
        <p class="text-sm text-zinc-500 dark:text-zinc-400 ml-14">
            Create password-protected, self-destructing, and metadata-cloaked redirect paths. Completely anonymous and Tor-friendly.
        </p>
    </div>

    {{-- Workspace Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

        {{-- Left: Settings / Inputs --}}
        <div class="lg:col-span-5 space-y-6">
            <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800/80 shadow-sm p-5 space-y-6">

                <div class="flex items-center justify-between pb-3 border-b border-zinc-200 dark:border-zinc-800">
                    <div class="flex items-center gap-2">
                        <div class="p-1.5 rounded-lg bg-violet-50 dark:bg-violet-900/30">
                            <flux:icon icon="link" class="size-3.5 text-violet-600 dark:text-violet-400" />
                        </div>
                        <h2 class="text-xs font-bold text-zinc-700 dark:text-zinc-200 uppercase tracking-widest">Shorten URL</h2>
                    </div>
                </div>

                {{-- Settings Form --}}
                <form wire:submit.prevent="shorten" class="space-y-4">

                    {{-- Target URL --}}
                    <flux:input
                        label="Destination URL"
                        type="url"
                        required
                        wire:model="original_url"
                        placeholder="https://example.com/very-long-path" />

                    {{-- Custom Alias --}}
                    <flux:input
                        label="Custom Alias (Optional)"
                        type="text"
                        wire:model="custom_code"
                        placeholder="my-secret-link" />

                    {{-- Target Network --}}
                    <flux:select label="Target Network" wire:model="connection_type">
                        <flux:select.option value="both">Both (Clearnet & Tor)</flux:select.option>
                        <flux:select.option value="clearnet">Clearnet Only (Standard Web)</flux:select.option>
                        <flux:select.option value="tor">Tor Only (.onion)</flux:select.option>
                    </flux:select>


                    {{-- Toggle Advanced Features --}}
                    <div class="pt-4 border-t border-zinc-100 dark:border-zinc-800/80 space-y-4">
                        <div class="flex items-center justify-between">
                            <label class="text-xs font-bold text-zinc-700 dark:text-zinc-200 uppercase tracking-widest">Advanced Features</label>
                            <label class="relative inline-flex items-center cursor-pointer select-none">
                                <input type="checkbox" wire:model.live="showAdvanced" class="sr-only peer" />
                                <div class="w-9 h-5 bg-zinc-200 dark:bg-zinc-800 rounded-full peer peer-focus:ring-0 peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-zinc-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-violet-600 dark:peer-checked:bg-violet-500"></div>
                            </label>
                        </div>

                        @if($showAdvanced)
                        <div class="space-y-4 pt-2">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                {{-- Expiration Option --}}
                                <flux:select label="Expires In" wire:model="expiry">
                                    <flux:select.option value="never">Never (Manual Delete)</flux:select.option>
                                    <flux:select.option value="1h">1 Hour</flux:select.option>
                                    <flux:select.option value="24h">24 Hours</flux:select.option>
                                    <flux:select.option value="7d">7 Days</flux:select.option>
                                    <flux:select.option value="30d">30 Days</flux:select.option>
                                </flux:select>

                                {{-- Click Limits --}}
                                <flux:input
                                    label="Max Clicks (Optional)"
                                    type="number"
                                    min="1"
                                    wire:model="max_clicks"
                                    placeholder="Unlimited" />
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                {{-- Access Password --}}
                                <flux:input
                                    label="Access Password (Optional)"
                                    type="password"
                                    wire:model="password"
                                    placeholder="No password" />

                                {{-- Burn after use toggle --}}
                                <div class="space-y-1.5 flex flex-col justify-center">
                                    <label class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Self-Destruct</label>
                                    <label class="flex items-center gap-2 cursor-pointer select-none">
                                        <input type="checkbox" wire:model="is_burn_after_use" class="rounded border-zinc-300 dark:border-zinc-700 text-zinc-800 focus:ring-zinc-400" />
                                        <span class="text-xs text-zinc-600 dark:text-zinc-400">Burn link after first click</span>
                                    </label>
                                </div>
                            </div>

                            {{-- Metadata Cloaking Toggle --}}
                            <div class="space-y-3 pt-3 border-t border-zinc-100 dark:border-zinc-800/80">
                                <div class="flex items-center justify-between">
                                    <span class="block text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">Preview Cloaking (OpenGraph)</span>
                                    <label class="relative inline-flex items-center cursor-pointer select-none">
                                        <input type="checkbox" wire:model.live="enableCloaking" class="sr-only peer" />
                                        <div class="w-9 h-5 bg-zinc-200 dark:bg-zinc-800 rounded-full peer peer-focus:ring-0 peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-zinc-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-violet-600 dark:peer-checked:bg-violet-500"></div>
                                    </label>
                                </div>
                                <p class="text-[11px] text-zinc-500 leading-normal">
                                    Spoof title and description shown by crawlers and previews. Real users are still redirected to the target URL.
                                </p>

                                @if($enableCloaking)
                                <div class="space-y-3 pt-2">
                                    <flux:input
                                        label="Spoof Title"
                                        type="text"
                                        wire:model="cloak_title"
                                        placeholder="e.g. Google Drive - Encrypted Folder" />

                                    <flux:input
                                        label="Spoof Description"
                                        type="text"
                                        wire:model="cloak_desc"
                                        placeholder="e.g. You have been invited to view this document." />
                                </div>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>

                    <flux:button type="submit" class="w-full bg-violet-600 hover:bg-violet-700 text-white font-bold py-2.5 rounded-xl shadow transition">
                        Create Short Link
                    </flux:button>
                </form>
            </div>
        </div>

        {{-- Right: Output & History --}}
        <div class="lg:col-span-7 space-y-6">

            {{-- Shortened Result Panel --}}
            @if($shortened_url)
            <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800/80 shadow-sm p-5 space-y-5">
                <div class="flex items-center justify-between pb-3 border-b border-zinc-200 dark:border-zinc-800">
                    <div class="flex items-center gap-2">
                        <div class="p-1.5 rounded-lg bg-emerald-50 dark:bg-emerald-950/20">
                            <flux:icon icon="check-circle" class="size-3.5 text-emerald-600 dark:text-emerald-400" />
                        </div>
                        <h2 class="text-xs font-bold text-zinc-700 dark:text-zinc-200 uppercase tracking-widest">Link Created Successfully</h2>
                    </div>
                    <flux:button size="xs" variant="ghost" wire:click="clearResults">Clear</flux:button>
                </div>

                {{-- Copy output link --}}
                <div class="space-y-3">
                    @if($new_link_details['connection_type'] === 'both')
                        <div class="space-y-2">
                            <span class="block text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Clearnet Connection Link</span>
                            <div class="flex gap-2">
                                <input
                                    type="text"
                                    readonly
                                    value="{{ $new_link_details['clearnet_url'] }}"
                                    id="result-link-clearnet"
                                    class="flex-1 px-3 py-2 bg-zinc-900 text-zinc-100 font-mono text-xs rounded-xl border-none focus:ring-0 select-all" />
                                <flux:button
                                    size="sm"
                                    variant="filled"
                                    class="bg-zinc-800 hover:bg-zinc-700 text-zinc-200 rounded-xl"
                                    onclick="navigator.clipboard.writeText(document.getElementById('result-link-clearnet').value); alert('Clearnet Link Copied!');">
                                    Copy
                                </flux:button>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <span class="block text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Tor (.onion) Connection Link</span>
                            <div class="flex gap-2">
                                <input
                                    type="text"
                                    readonly
                                    value="{{ $new_link_details['tor_url'] }}"
                                    id="result-link-tor"
                                    class="flex-1 px-3 py-2 bg-zinc-900 text-zinc-100 font-mono text-xs rounded-xl border-none focus:ring-0 select-all" />
                                <flux:button
                                    size="sm"
                                    variant="filled"
                                    class="bg-zinc-800 hover:bg-zinc-700 text-zinc-200 rounded-xl"
                                    onclick="navigator.clipboard.writeText(document.getElementById('result-link-tor').value); alert('Tor Link Copied!');">
                                    Copy
                                </flux:button>
                            </div>
                        </div>
                    @else
                        <div class="space-y-2">
                            <span class="block text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Shortened URL</span>
                            <div class="flex gap-2">
                                <input
                                    type="text"
                                    readonly
                                    value="{{ $shortened_url }}"
                                    id="result-link"
                                    class="flex-1 px-3 py-2 bg-zinc-900 text-zinc-100 font-mono text-xs rounded-xl border-none focus:ring-0 select-all" />
                                <flux:button
                                    size="sm"
                                    variant="filled"
                                    class="bg-zinc-800 hover:bg-zinc-700 text-zinc-200 rounded-xl"
                                    onclick="navigator.clipboard.writeText(document.getElementById('result-link').value); alert('Short Link Copied!');">
                                    Copy
                                </flux:button>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- QR Code and details split --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
                    @if($qr_code_uri)
                    <div class="flex flex-col items-center justify-center p-4 bg-zinc-50 dark:bg-zinc-900/60 border border-zinc-200 dark:border-zinc-800 ">
                        <span class="block text-[9px] font-bold text-zinc-400 uppercase tracking-wider mb-2">Scan QR Code</span>
                        <img src="{{ $qr_code_uri }}" class="size-32 rounded-xl object-contain shadow-sm" alt="Short URL QR Code" />
                    </div>
                    @endif

                    <div class="space-y-3 text-xs leading-normal">
                        <span class="block text-[9px] font-bold text-zinc-400 uppercase tracking-wider">Configuration Summary</span>

                        <div class="space-y-1.5">
                            <div class="flex justify-between py-1 border-b border-zinc-100 dark:border-zinc-800/40">
                                <span class="text-zinc-500">Destination:</span>
                                <span class="font-semibold text-zinc-800 dark:text-zinc-200 break-all text-right max-w-[160px]">{{ $new_link_details['original_url'] }}</span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-zinc-100 dark:border-zinc-800/40">
                                <span class="text-zinc-500">Target Network:</span>
                                <span class="font-semibold text-zinc-850 dark:text-zinc-200 uppercase text-[9px]">
                                    {{ $new_link_details['connection_type'] }}
                                </span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-zinc-100 dark:border-zinc-800/40">
                                <span class="text-zinc-500">Security Password:</span>
                                <span class="font-semibold {{ $new_link_details['has_password'] ? 'text-amber-500' : 'text-zinc-400' }}">
                                    {{ $new_link_details['has_password'] ? 'Enabled' : 'None' }}
                                </span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-zinc-100 dark:border-zinc-800/40">
                                <span class="text-zinc-500">Expiration Time:</span>
                                <span class="font-semibold text-zinc-800 dark:text-zinc-200">{{ $new_link_details['expires_at'] }}</span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-zinc-100 dark:border-zinc-800/40">
                                <span class="text-zinc-500">Click Limit:</span>
                                <span class="font-semibold text-zinc-800 dark:text-zinc-200">{{ $new_link_details['max_clicks'] }}</span>
                            </div>
                            <div class="flex justify-between py-1">
                                <span class="text-zinc-500">Burn on Read:</span>
                                <span class="font-semibold {{ $new_link_details['is_burn_after_use'] ? 'text-red-500' : 'text-zinc-400' }}">
                                    {{ $new_link_details['is_burn_after_use'] ? 'Active' : 'No' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Supported Redirection & Cloaking Schemes --}}
            <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800/80 shadow-sm p-5 space-y-4">
                <div class="pb-3 border-b border-zinc-200 dark:border-zinc-800">
                    <div class="flex items-center gap-2">
                        <div class="p-1.5 rounded-lg bg-violet-50 dark:bg-violet-900/30">
                            <flux:icon icon="sparkles" class="size-3.5 text-violet-600 dark:text-violet-400" />
                        </div>
                        <h2 class="text-xs font-bold text-zinc-700 dark:text-zinc-200 uppercase tracking-widest">Supported Redirection & Cloaking Schemes</h2>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    {{-- 1. Secure Link Sharing --}}
                    <div class="p-4 bg-zinc-50 dark:bg-zinc-800/20 border border-zinc-200/60 dark:border-zinc-800 rounded-2xl space-y-2">
                        <div class="flex items-center gap-2">
                            <flux:icon icon="lock-closed" class="size-4 text-violet-600 dark:text-violet-400" />
                            <h4 class="text-xs font-bold text-zinc-800 dark:text-zinc-200">Secure Link Gateways</h4>
                        </div>
                        <p class="text-[11px] text-zinc-500 leading-normal">
                            Protects sensitive destination URLs behind a password wall, shielding assets from web-crawlers and malicious scanners.
                        </p>
                    </div>

                    {{-- 2. Metadata Spoofing --}}
                    <div class="p-4 bg-zinc-50 dark:bg-zinc-800/20 border border-zinc-200/60 dark:border-zinc-800 rounded-2xl space-y-2">
                        <div class="flex items-center gap-2">
                            <flux:icon icon="eye-slash" class="size-4 text-violet-600 dark:text-violet-400" />
                            <h4 class="text-xs font-bold text-zinc-800 dark:text-zinc-200">Metadata Spoofing</h4>
                        </div>
                        <p class="text-[11px] text-zinc-500 leading-normal">
                            Overrides OpenGraph tags to customize title/description previews parsed by crawlers, showing generic metadata while keeping destination intact.
                        </p>
                    </div>

                    {{-- 3. Burn-on-Read --}}
                    <div class="p-4 bg-zinc-50 dark:bg-zinc-800/20 border border-zinc-200/60 dark:border-zinc-800 rounded-2xl space-y-2">
                        <div class="flex items-center gap-2">
                            <flux:icon icon="fire" class="size-4 text-violet-600 dark:text-violet-400" />
                            <h4 class="text-xs font-bold text-zinc-800 dark:text-zinc-200">Disposable (Burner) Links</h4>
                        </div>
                        <p class="text-[11px] text-zinc-500 leading-normal">
                            Self-destructs the redirection database record instantly after the first click, ensuring it can never be accessed or verified twice.
                        </p>
                    </div>

                    {{-- 4. Privacy Gatekeeper --}}
                    <div class="p-4 bg-zinc-50 dark:bg-zinc-800/20 border border-zinc-200/60 dark:border-zinc-800 rounded-2xl space-y-2">
                        <div class="flex items-center gap-2">
                            <flux:icon icon="shield-check" class="size-4 text-violet-600 dark:text-violet-400" />
                            <h4 class="text-xs font-bold text-zinc-800 dark:text-zinc-200">Privacy Sanitizer</h4>
                        </div>
                        <p class="text-[11px] text-zinc-500 leading-normal">
                            Strips tracking query parameters and headers, routing users anonymously to onion or clearweb endpoints without leaking tracking tags.
                        </p>
                    </div>
                </div>
            </div>

            {{-- History Card --}}
            <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800/80 shadow-sm p-5 space-y-4">
                <div class="pb-3 border-b border-zinc-200 dark:border-zinc-800">
                    <div class="flex items-center gap-2">
                        <div class="p-1.5 rounded-lg bg-violet-50 dark:bg-violet-900/30">
                            <flux:icon icon="clock" class="size-3.5 text-violet-600 dark:text-violet-400" />
                        </div>
                        <h2 class="text-xs font-bold text-zinc-700 dark:text-zinc-200 uppercase tracking-widest">Active Session Links</h2>
                    </div>
                </div>

                @php $history = $this->getHistory(); @endphp

                @if(count($history) > 0)
                <div class="overflow-x-auto border border-zinc-200 dark:border-zinc-800 rounded-xl">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-zinc-50 dark:bg-zinc-800/40 border-b border-zinc-200 dark:border-zinc-800 text-zinc-400 dark:text-zinc-500 font-bold uppercase tracking-wider">
                                <th class="p-3">Short Path</th>
                                <th class="p-3">Destination</th>
                                <th class="p-3 text-center">Clicks</th>
                                <th class="p-3">Status / Expiry</th>
                                <th class="p-3 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                            @foreach($history as $link)
                            <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/20">
                                <td class="p-3 font-mono font-semibold text-violet-600 dark:text-violet-400">
                                    <a href="{{ route('short-link.redirect', ['code' => $link->code]) }}" target="_blank" class="hover:underline">
                                        /s/{{ $link->code }}
                                    </a>
                                </td>
                                <td class="p-3 font-mono text-[11px] text-zinc-500 max-w-[150px] truncate" title="{{ $link->original_url }}">
                                    {{ $link->original_url }}
                                </td>
                                <td class="p-3 text-center font-bold text-zinc-700 dark:text-zinc-300">
                                    {{ $link->clicks_count }}
                                    @if($link->max_clicks)
                                    <span class="text-zinc-400 dark:text-zinc-500 font-normal">/ {{ $link->max_clicks }}</span>
                                    @endif
                                </td>
                                <td class="p-3 text-zinc-600 dark:text-zinc-400">
                                    @if($link->is_burn_after_use)
                                    <span class="px-2 py-0.5 rounded bg-red-500/10 text-red-500 font-semibold text-[10px]">BURNER</span>
                                    @elseif($link->expires_at)
                                    <span class="font-mono text-[10px]">{{ $link->expires_at->format('Y-m-d H:i') }}</span>
                                    @else
                                    <span class="text-zinc-400 font-semibold text-[10px]">ACTIVE</span>
                                    @endif

                                    @if($link->password)
                                    <span class="ml-1 inline-block" title="Password Protected">🔒</span>
                                    @endif

                                    <span class="ml-1 px-1.5 py-0.5 rounded bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 font-semibold text-[9px] uppercase tracking-wider">
                                        {{ $link->connection_type }}
                                    </span>
                                </td>
                                <td class="p-3 text-right">
                                    <flux:button
                                        size="xs"
                                        variant="ghost"
                                        class="hover:text-red-600 dark:hover:text-red-400"
                                        wire:click="deleteLink('{{ $link->code }}')">
                                        Delete
                                    </flux:button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="flex flex-col items-center justify-center py-12 text-zinc-400 dark:text-zinc-500 text-xs gap-2 text-center">
                    <flux:icon icon="information-circle" class="size-8 text-zinc-300 dark:text-zinc-700" />
                    <p class="font-semibold text-zinc-600 dark:text-zinc-400">No active links created in this session</p>
                    <p class="text-zinc-500">Links created will appear here for easy management and quick analytics.</p>
                </div>
                @endif
            </div>

        </div>

    </div>

</div>