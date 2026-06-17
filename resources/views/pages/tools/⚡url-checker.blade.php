<?php

use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('URL Security & Status Analyzer')] class extends Component
{
    public string $url = '';
    public bool $isAnalyzed = false;

    // Parsed results
    public string $parsedScheme = '';
    public string $parsedHost = '';
    public string $parsedPath = '';
    public string $connectionType = ''; // clearnet, tor

    // Security Checks
    public string $riskLevel = 'SAFE'; // SAFE, WARNING, DANGER
    public int $threatScore = 0; // 0 to 100
    public array $securityWarnings = [];
    public array $detectedParameters = [];

    // Live Connection Checks
    public bool $checkOnline = false;
    public string $pingStatus = 'UNTESTED'; // UNTESTED, ONLINE, OFFLINE, PROXY_ERROR
    public ?int $httpCode = null;
    public ?float $responseTime = null;
    public string $serverHeader = '';
    public string $proxyUsed = '';

    public function analyze()
    {
        $this->validate([
            'url' => 'required|url'
        ]);

        $this->isAnalyzed = true;
        $this->resetAnalysis();

        // 1. Parse URL elements
        $parsed = parse_url($this->url);
        $this->parsedScheme = strtolower($parsed['scheme'] ?? 'http');
        $this->parsedHost = strtolower($parsed['host'] ?? '');
        $this->parsedPath = $parsed['path'] ?? '';
        $query = $parsed['query'] ?? '';

        // 2. Identify connection type (Tor vs Clearnet)
        if (str_ends_with($this->parsedHost, '.onion')) {
            $this->connectionType = 'tor';
        } else {
            $this->connectionType = 'clearnet';
        }

        // 3. Security Check: Protocol
        if ($this->parsedScheme === 'http') {
            if ($this->connectionType === 'clearnet') {
                $this->securityWarnings[] = [
                    'type' => 'danger',
                    'text' => 'Insecure protocol: HTTP is not encrypted over Clearnet. Attackers can eavesdrop or modify traffic.'
                ];
                $this->threatScore += 30;
            } else {
                // Tor onion addresses handle end-to-end encryption inherently. http is normal.
                $this->securityWarnings[] = [
                    'type' => 'info',
                    'text' => 'HTTP is used, but safe: On Tor Onion (.onion) hidden services, the network natively secures end-to-end encryption without SSL certificates.'
                ];
            }
        }

        // 4. Security Check: Suspect TLDs (only for clearnet)
        if ($this->connectionType === 'clearnet') {
            $abusedTlds = ['.zip', '.mov', '.click', '.gq', '.cf', '.ml', '.tk', '.men', '.work', '.top', '.loan'];
            foreach ($abusedTlds as $tld) {
                if (str_ends_with($this->parsedHost, $tld)) {
                    $this->securityWarnings[] = [
                        'type' => 'warning',
                        'text' => "Suspicious TLD: The extension '{$tld}' is frequently abused for phishing, malware distribution, or spam campaigns."
                    ];
                    $this->threatScore += 25;
                    break;
                }
            }
        }

        // 5. Security Check: Phishing keywords in subdomains
        $phishingKeywords = ['login', 'signin', 'secure', 'wallet', 'verification', 'verify', 'update', 'account', 'paypal', 'binance', 'coinbase', 'blockchain'];
        foreach ($phishingKeywords as $keyword) {
            // Check if keyword is inside host but not the main domain (e.g. login.example.com vs examplelogin.com)
            if (str_contains($this->parsedHost, $keyword)) {
                $parts = explode('.', $this->parsedHost);
                // If it's a subdomain part
                if (count($parts) > 2 && in_array($keyword, array_slice($parts, 0, count($parts) - 2))) {
                    $this->securityWarnings[] = [
                        'type' => 'danger',
                        'text' => "Phishing Indicator: Subdomain contains the keyword '{$keyword}', which is a common trick to masquerade as legitimate portals."
                    ];
                    $this->threatScore += 35;
                    break;
                }
            }
        }

        // 6. Security Check: IP-based Host
        if (filter_var($this->parsedHost, FILTER_VALIDATE_IP)) {
            $this->securityWarnings[] = [
                'type' => 'danger',
                'text' => 'Raw IP Address used: Navigating directly to an IP address instead of a domain name is highly typical of malware nodes or ad-hoc phishing servers.'
            ];
            $this->threatScore += 40;
        }

        // 7. Parse tracking parameters & potential injections
        if ($query) {
            parse_str($query, $params);
            $trackingKeys = ['utm_source', 'utm_medium', 'utm_campaign', 'fbclid', 'gclid', 'yclid', 'affiliate', 'ref'];
            $injectionPatterns = ['\'', '"', '<script', 'union', 'select', 'concat', 'char', 'drop', 'insert'];

            foreach ($params as $key => $val) {
                // Check for tracking parameters
                if (in_array(strtolower($key), $trackingKeys)) {
                    $this->detectedParameters[] = [
                        'type' => 'tracking',
                        'name' => $key,
                        'value' => $val,
                        'desc' => 'Tracking parameter used to construct analytics profiles of your navigation habits.'
                    ];
                }

                // Check for potential exploitation/injection payloads
                foreach ($injectionPatterns as $pattern) {
                    if (str_contains(strtolower($val), $pattern)) {
                        $this->detectedParameters[] = [
                            'type' => 'injection',
                            'name' => $key,
                            'value' => $val,
                            'desc' => "Potential SQL injection or XSS payload pattern '{$pattern}' detected in query variable."
                        ];
                        $this->securityWarnings[] = [
                            'type' => 'danger',
                            'text' => "Malicious pattern in URL query: Parameter '{$key}' contains structures matching SQL injection or Script payloads."
                        ];
                        $this->threatScore += 20;
                        break;
                    }
                }
            }
        }

        // Determine final risk rating
        if ($this->threatScore >= 50) {
            $this->riskLevel = 'DANGER';
        } elseif ($this->threatScore > 0) {
            $this->riskLevel = 'WARNING';
        } else {
            $this->riskLevel = 'SAFE';
        }

        // 8. Live connection check (if requested)
        if ($this->checkOnline) {
            $this->performConnectionCheck();
        }
    }

    private function performConnectionCheck()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_NOBODY, true); // HEAD request only to save bandwidth
        curl_setopt($ch, CURLOPT_TIMEOUT, 4); // Fast timeout
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Do not fail on expired certificates for forensic checks
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; rv:109.0) Gecko/20100101 Firefox/115.0'); // Tor-like user agent

        // Handle Tor proxy if it's an onion address
        if ($this->connectionType === 'tor') {
            $proxy = env('TOR_SOCKS_PROXY', '127.0.0.1:9050');
            $this->proxyUsed = $proxy;
            curl_setopt($ch, CURLOPT_PROXY, $proxy);
            curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5_HOSTNAME);
        }

        $startTime = microtime(true);
        $response = curl_exec($ch);
        $this->responseTime = round((microtime(true) - $startTime) * 1000, 2); // Milliseconds

        if ($response === false) {
            $errorNo = curl_errno($ch);
            $errorMsg = curl_error($ch);
            curl_close($ch);

            if ($this->connectionType === 'tor' && in_array($errorNo, [CURLE_COULDNT_RESOLVE_PROXY, CURLE_COULDNT_CONNECT])) {
                $this->pingStatus = 'PROXY_ERROR';
                $this->serverHeader = "Failed to communicate with Tor SOCKS5 proxy at '{$this->proxyUsed}'. Ensure the Tor service is running locally.";
            } else {
                $this->pingStatus = 'OFFLINE';
                $this->serverHeader = "Connection Failed: {$errorMsg} (Code: {$errorNo})";
            }
            $this->httpCode = null;
        } else {
            $this->httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $serverHeader = '';

            // Extract Server header if possible
            $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            $headerText = substr($response, 0, $headerSize);
            foreach (explode("\r\n", $headerText) as $line) {
                if (stripos($line, 'Server:') === 0) {
                    $serverHeader = trim(substr($line, 7));
                    break;
                }
            }
            curl_close($ch);

            $this->pingStatus = 'ONLINE';
            $this->serverHeader = $serverHeader ?: 'Server header obscured or not provided.';
        }
    }

    private function resetAnalysis()
    {
        $this->parsedScheme = '';
        $this->parsedHost = '';
        $this->parsedPath = '';
        $this->connectionType = '';
        $this->riskLevel = 'SAFE';
        $this->threatScore = 0;
        $this->securityWarnings = [];
        $this->detectedParameters = [];
        $this->pingStatus = 'UNTESTED';
        $this->httpCode = null;
        $this->responseTime = null;
        $this->serverHeader = '';
        $this->proxyUsed = '';
    }
};
?>

<div class="min-h-screen pb-16 space-y-8">

    {{-- Page Header --}}
    <div class="flex items-center gap-3">
        <div class="p-2  text-violet-600 dark:text-violet-400">
            <flux:icon icon="shield-check" class="size-7" />
        </div>
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white tracking-tight">URL Security & Status Analyzer</h1>
            <p class="text-xs text-zinc-500 dark:text-zinc-400">
                Input a URL to execute instant heuristic safety scans and live connectivity diagnostics.
            </p>
        </div>
    </div>

    {{-- Input Section --}}
    <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-sm p-6 space-y-6">
        <form wire:submit="analyze" class="space-y-4">
            <div class="flex flex-col  justify-center gap-2">

                {{-- URL Input --}}
                <div class="space-y-2">
                    <label for="url" class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-widest">Target URL</label>
                    <input
                        type="url"
                        wire:model="url"
                        id="url"
                        placeholder="https://example.com/login?utm_source=tracker"
                        required
                        class="w-full px-4 py-2.5 rounded-md bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700  text-sm focus:outline-none focus:ring-2 focus:ring-violet-500 dark:focus:ring-violet-400 transition font-mono text-zinc-800 dark:text-zinc-100" />
                    @error('url') <span class="text-xs text-red-500 font-semibold">{{ $message }}</span> @enderror
                </div>

                {{-- Trigger Button --}}
                <div class="flex items-end">
                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        class="w-full py-2.5 bg-violet-600 rounded-md hover:bg-violet-700 text-white font-bold  text-sm shadow transition h-[42px] flex items-center justify-center gap-2">
                        <span wire:loading.remove wire:target="analyze">Analyze URL</span>
                        <span wire:loading wire:target="analyze" class="flex items-center gap-2" x-cloak>
                            <flux:icon icon="loading" class="size-4 animate-spin text-white" />
                            Analyzing...
                        </span>
                    </button>
                </div>

            </div>

            {{-- Connection Check Toggle --}}
            <div class="flex items-center gap-2 pt-2">
                <input
                    type="checkbox"
                    wire:model="checkOnline"
                    id="checkOnline"
                    class="rounded border-zinc-300 dark:border-zinc-700 text-violet-600 focus:ring-violet-500 dark:bg-zinc-800" />
                <label for="checkOnline" class="text-xs text-zinc-600 dark:text-zinc-400 cursor-pointer select-none font-semibold">
                    Check network connectivity status (Live cURL fetch)
                </label>
            </div>
        </form>
    </div>

    {{-- Result Section --}}
    @if($isAnalyzed)
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-start">

        {{-- Column 1: Security Report --}}
        <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-sm p-6 space-y-6">

            {{-- Heading --}}
            <div class="flex items-center gap-2.5 pb-3 border-b border-zinc-100 dark:border-zinc-800">
                <div class="p-1.5 rounded-lg bg-violet-50 dark:bg-violet-900/30 text-violet-600 dark:text-violet-400">
                    <flux:icon icon="exclamation-triangle" class="size-4" />
                </div>
                <h3 class="text-xs font-bold text-zinc-700 dark:text-zinc-200 uppercase tracking-widest">Heuristic Security Report</h3>
            </div>

            {{-- Threat Score Indicator --}}
            @php
            $indicatorBg = match($riskLevel) {
            'SAFE' => 'border-emerald-200/60 dark:border-emerald-950/40 bg-emerald-50/10 dark:bg-emerald-950/5',
            'WARNING' => 'border-amber-200/60 dark:border-amber-950/40 bg-amber-50/10 dark:bg-amber-950/5',
            default => 'border-red-200/60 dark:border-red-950/40 bg-red-50/10 dark:bg-red-950/5',
            };
            @endphp
            <div class="flex items-center justify-between p-4 rounded-2xl border {{ $indicatorBg }} shadow-inner">
                <div>
                    <div class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">Safety Rating</div>
                    <div class="flex items-center gap-1.5 mt-1">
                        @if($riskLevel === 'SAFE')
                        <span class="px-2 py-0.5 bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 text-[10px] font-extrabold uppercase tracking-wider rounded-md">Safe</span>
                        @elseif($riskLevel === 'WARNING')
                        <span class="px-2 py-0.5 bg-amber-500/20 text-amber-600 dark:text-amber-400 text-[10px] font-extrabold uppercase tracking-wider rounded-md">Warning</span>
                        @else
                        <span class="px-2 py-0.5 bg-red-500/20 text-red-600 dark:text-red-400 text-[10px] font-extrabold uppercase tracking-wider rounded-md">Danger</span>
                        @endif
                        <span class="text-xs text-zinc-600 dark:text-zinc-300 font-bold ml-1">Score: {{ $threatScore }}/100</span>
                    </div>
                </div>

                {{-- Connection Type Badge --}}
                <div>
                    <div class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider text-right">Domain Type</div>
                    @if($connectionType === 'tor')
                    <span class="inline-flex items-center gap-1 mt-1 px-2 py-0.5 bg-purple-500/10 text-purple-400 text-[10px] font-extrabold uppercase tracking-wider rounded-md">
                        🧅 Tor Onion
                    </span>
                    @else
                    <span class="inline-flex items-center gap-1 mt-1 px-2 py-0.5 bg-blue-500/10 text-blue-400 text-[10px] font-extrabold uppercase tracking-wider rounded-md">
                        🌐 Clearnet
                    </span>
                    @endif
                </div>
            </div>

            {{-- Security Check Warnings List --}}
            <div class="space-y-3">
                <div class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">Analysis Logs</div>
                @if(count($securityWarnings) > 0)
                <div class="space-y-2">
                    @foreach($securityWarnings as $warning)
                    @php
                    $warningColors = match($warning['type']) {
                    'info' => 'bg-emerald-500/5 border-emerald-500/10 text-zinc-600 dark:text-zinc-400',
                    'warning' => 'bg-amber-500/5 border-amber-500/10 text-zinc-600 dark:text-zinc-400',
                    default => 'bg-red-500/5 border-red-500/10 text-zinc-600 dark:text-zinc-400',
                    };
                    $warningIcon = match($warning['type']) {
                    'info' => '✅',
                    'warning' => '⚠️',
                    default => '🚨',
                    };
                    @endphp
                    <div class="flex items-start gap-2.5 p-3 border rounded-xl text-xs leading-normal {{ $warningColors }}">
                        <span class="shrink-0 mt-0.5">{{ $warningIcon }}</span>
                        <span>{{ $warning['text'] }}</span>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="p-4 border border-dashed border-zinc-200 dark:border-zinc-800 rounded-xl text-xs text-zinc-400 dark:text-zinc-500 text-center font-medium italic">
                    No potential phishing indicators or scheme threats detected.
                </div>
                @endif
            </div>

            {{-- Parameter Breakdown --}}
            <div class="space-y-3">
                <div class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">URL Parameter Analysis</div>
                @if(count($detectedParameters) > 0)
                <div class="space-y-2.5">
                    @foreach($detectedParameters as $param)
                    <div class="p-3 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800/80 rounded-xl space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="font-mono text-xs font-semibold text-zinc-700 dark:text-zinc-300 break-all select-all">
                                {{ $param['name'] }} = <span class="text-zinc-500 dark:text-zinc-400">{{ $param['value'] }}</span>
                            </span>
                            @if($param['type'] === 'tracking')
                            <span class="text-[9px] font-bold uppercase tracking-wider text-blue-500 bg-blue-500/10 px-1.5 py-0.5 rounded">Tracking</span>
                            @else
                            <span class="text-[9px] font-bold uppercase tracking-wider text-red-500 bg-red-500/10 px-1.5 py-0.5 rounded">Suspicious</span>
                            @endif
                        </div>
                        <p class="text-[11px] text-zinc-500 dark:text-zinc-400 leading-normal">{{ $param['desc'] }}</p>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="p-4 border border-dashed border-zinc-200 dark:border-zinc-800 rounded-xl text-xs text-zinc-400 dark:text-zinc-500 text-center font-medium italic">
                    No tracking variables or script structures parsed in query string.
                </div>
                @endif
            </div>
        </div>

        {{-- Column 2: Connectivity & Server Diagnostics --}}
        <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-sm p-6 space-y-6">

            {{-- Heading --}}
            <div class="flex items-center gap-2.5 pb-3 border-b border-zinc-100 dark:border-zinc-800">
                <div class="p-1.5 rounded-lg bg-violet-50 dark:bg-violet-900/30 text-violet-600 dark:text-violet-400">
                    <flux:icon icon="globe-alt" class="size-4" />
                </div>
                <h3 class="text-xs font-bold text-zinc-700 dark:text-zinc-200 uppercase tracking-widest">Connectivity Diagnostics</h3>
            </div>

            @if($checkOnline)
            {{-- Status Badge --}}
            @php
            $statusBg = match($pingStatus) {
            'ONLINE' => 'border-emerald-200/60 dark:border-emerald-950/40 bg-emerald-50/10 dark:bg-emerald-950/5',
            'PROXY_ERROR' => 'border-amber-200/60 dark:border-amber-950/40 bg-amber-50/10 dark:bg-amber-950/5',
            default => 'border-red-200/60 dark:border-red-950/40 bg-red-50/10 dark:bg-red-950/5',
            };
            @endphp
            <div class="flex items-center justify-between p-4 rounded-2xl border {{ $statusBg }} shadow-inner font-semibold">
                <div>
                    <div class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">Connection State</div>
                    <div class="flex items-center gap-1.5 mt-1">
                        @if($pingStatus === 'ONLINE')
                        <span class="px-2 py-0.5 bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 text-[10px] font-extrabold uppercase tracking-wider rounded-md">Online</span>
                        <span class="text-xs text-emerald-600 dark:text-emerald-400 font-bold">{{ $httpCode }} OK</span>
                        @elseif($pingStatus === 'OFFLINE')
                        <span class="px-2 py-0.5 bg-red-500/20 text-red-600 dark:text-red-400 text-[10px] font-extrabold uppercase tracking-wider rounded-md">Offline</span>
                        <span class="text-xs text-red-600 dark:text-red-400 font-bold">Unreachable</span>
                        @elseif($pingStatus === 'PROXY_ERROR')
                        <span class="px-2 py-0.5 bg-amber-500/20 text-amber-600 dark:text-amber-400 text-[10px] font-extrabold uppercase tracking-wider rounded-md">Proxy Error</span>
                        <span class="text-xs text-amber-600 dark:text-amber-400 font-bold">Tor Proxy Offline</span>
                        @endif
                    </div>
                </div>

                {{-- Response Time --}}
                @if($responseTime !== null)
                <div>
                    <div class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider text-right">Fetch Time</div>
                    <div class="text-sm font-mono font-bold text-zinc-800 dark:text-zinc-200 text-right mt-1">{{ $responseTime }} ms</div>
                </div>
                @endif
            </div>

            {{-- Network details --}}
            <div class="space-y-4">
                {{-- Host & Target --}}
                <div class="grid grid-cols-2 gap-4">
                    <div class="p-3 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800/80 rounded-xl">
                        <span class="block text-[9px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">Protocol Scheme</span>
                        <span class="font-mono text-xs font-semibold text-zinc-700 dark:text-zinc-300 mt-1 uppercase">{{ $parsedScheme }}</span>
                    </div>
                    <div class="p-3 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800/80 rounded-xl">
                        <span class="block text-[9px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">Target Domain</span>
                        <span class="font-mono text-xs font-semibold text-zinc-700 dark:text-zinc-300 mt-1 break-all">{{ $parsedHost }}</span>
                    </div>
                </div>

                {{-- Tor Proxy Routing logs --}}
                @if($connectionType === 'tor')
                <div class="p-3.5 bg-purple-500/5 border border-purple-500/10 rounded-xl space-y-1">
                    <span class="block text-[9px] font-bold text-purple-400 uppercase tracking-wider">Tor Proxy Routing</span>
                    <p class="text-[11px] text-zinc-500 dark:text-zinc-400 leading-normal">
                        Requested Onion domain. Connection routed locally through SOCKS5 host proxy at: <code class="font-mono text-xs px-1 py-0.5 bg-zinc-100 dark:bg-zinc-800 rounded text-zinc-700 dark:text-zinc-300 font-semibold">{{ $proxyUsed }}</code>
                    </p>
                </div>
                @endif

                {{-- Server response headers --}}
                <div class="space-y-2">
                    <span class="block text-[9px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">Diagnostic Response / Headers</span>
                    <div class="font-mono text-xs bg-zinc-50 dark:bg-zinc-950/60 border border-zinc-200 dark:border-zinc-800 p-3.5 rounded-xl text-zinc-700 dark:text-zinc-300 leading-normal break-all min-h-[60px] select-all">
                        {{ $serverHeader }}
                    </div>
                </div>
            </div>
            @else
            {{-- Connectivity Check disabled warning --}}
            <div class="flex flex-col items-center justify-center text-center p-8 border border-dashed border-zinc-200 dark:border-zinc-800 rounded-2xl space-y-3">
                <div class="p-3 rounded-full bg-zinc-100 dark:bg-zinc-800 text-zinc-400 dark:text-zinc-600">
                    <flux:icon icon="bolt-slash" class="size-8" />
                </div>
                <div class="space-y-1">
                    <h4 class="text-xs font-bold text-zinc-700 dark:text-zinc-300">Live Fetch Disabled</h4>
                    <p class="text-[11px] text-zinc-500 dark:text-zinc-400 leading-normal max-w-[240px]">
                        Toggle the "Check network connectivity status" option to run active ping fetches and retrieve HTTP headers.
                    </p>
                </div>
            </div>
            @endif
        </div>

    </div>
    @endif

</div>