<?php

use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Multi Search & Service Gateways')] class extends Component
{
    // Single File Component logic
};
?>

<div class="min-h-screen pb-16 space-y-8" x-data="{ query: '', activeTab: 'search' }">

    {{-- Page Header --}}
    <div class="flex items-center gap-3">
        <div class="p-2 text-violet-600 dark:text-violet-400">
            <flux:icon icon="magnifying-glass" class="size-7" />
        </div>
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white tracking-tight">Multi Search & Service Gateways</h1>
            <p class="text-xs text-zinc-500 dark:text-zinc-400">
                Direct query forms targeting public search indexers, translation proxies, Tor network nodes, reference libraries, and cryptographic key vaults.
            </p>
        </div>
    </div>

    {{-- Master Sync Panel --}}
    <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-sm p-6">
        <div class="space-y-2">
            <label class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-widest">Master Search Query</label>
            <div class="relative">
                <input
                    type="text"
                    x-model="query"
                    placeholder="Type your search term here... All fields on the page will automatically sync!"
                    class="w-full pl-11 pr-4 py-3 rounded-xl bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800/80 text-sm focus:outline-none focus:ring-2 focus:ring-violet-500 dark:focus:ring-violet-400 transition font-mono text-zinc-850 dark:text-zinc-100 placeholder-zinc-400 dark:placeholder-zinc-600" />
                <div class="absolute left-4 top-1/2 -translate-y-1/2 text-zinc-400 dark:text-zinc-500">
                    <flux:icon icon="magnifying-glass" class="size-5" />
                </div>
            </div>
        </div>
    </div>

    {{-- Category Tabs --}}
    <div class="flex flex-wrap gap-2 pb-2 border-b border-zinc-200 dark:border-zinc-800">
        <button
            @click="activeTab = 'search'"
            :class="activeTab === 'search' ? 'bg-violet-600 text-white shadow-sm' : 'text-zinc-600 dark:text-zinc-400 bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-800 dark:hover:bg-zinc-800 border border-zinc-200 dark:border-zinc-800/80'"
            class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5">
            <flux:icon icon="magnifying-glass" class="size-3.5" />
            General & Onion Search
        </button>
        <button
            @click="activeTab = 'translation'"
            :class="activeTab === 'translation' ? 'bg-violet-600 text-white shadow-sm' : 'text-zinc-600 dark:text-zinc-400 bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-800 dark:hover:bg-zinc-800 border border-zinc-200 dark:border-zinc-800/80'"
            class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5">
            <flux:icon icon="language" class="size-3.5" />
            Translation & Reference
        </button>
        <button
            @click="activeTab = 'socials'"
            :class="activeTab === 'socials' ? 'bg-violet-600 text-white shadow-sm' : 'text-zinc-600 dark:text-zinc-400 bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-800 dark:hover:bg-zinc-800 border border-zinc-200 dark:border-zinc-800/80'"
            class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5">
            <flux:icon icon="chat-bubble-left-right" class="size-3.5" />
            Socials & Media
        </button>
        <button
            @click="activeTab = 'libraries'"
            :class="activeTab === 'libraries' ? 'bg-violet-600 text-white shadow-sm' : 'text-zinc-600 dark:text-zinc-400 bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-800 dark:hover:bg-zinc-800 border border-zinc-200 dark:border-zinc-800/80'"
            class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5">
            <flux:icon icon="academic-cap" class="size-3.5" />
            Libraries & Crypto
        </button>
        <button
            @click="activeTab = 'boorus'"
            :class="activeTab === 'boorus' ? 'bg-violet-600 text-white shadow-sm' : 'text-zinc-600 dark:text-zinc-400 bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-800 dark:hover:bg-zinc-800 border border-zinc-200 dark:border-zinc-800/80'"
            class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5">
            <flux:icon icon="photo" class="size-3.5" />
            Anime & Boorus
        </button>
    </div>

    {{-- Content --}}
    <div class="space-y-6">

        {{-- ==================== TAB 1: GENERAL & ONION SEARCH ==================== --}}
        <div x-show="activeTab === 'search'" class="space-y-8" x-cloak>



            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- Web Search Engines Card --}}
                <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-sm p-6 space-y-6">
                    <div class="flex items-center gap-2.5 pb-3 border-b border-zinc-100 dark:border-zinc-800">
                        <div class="p-1.5 rounded-lg bg-violet-50 dark:bg-violet-900/30 text-violet-600 dark:text-violet-400">
                            <flux:icon icon="globe-alt" class="size-4" />
                        </div>
                        <h3 class="text-xs font-bold text-zinc-700 dark:text-zinc-200 uppercase tracking-widest">Web Search Engines</h3>
                    </div>

                    <form method="GET" rel="noreferrer" target="_blank" class="space-y-4">
                        <div class="space-y-2">
                            <label class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">Search Term</label>
                            <input
                                type="text"
                                name="q"
                                x-model="query"
                                placeholder="Enter keyword"
                                required
                                class="w-full px-3 py-2 rounded-lg bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 text-xs font-mono text-zinc-800 dark:text-zinc-200 focus:outline-none focus:ring-1 focus:ring-violet-500" />
                        </div>

                        <div class="grid grid-cols-2 gap-2 pt-2">
                            <button type="submit" formaction="https://search.brave4u7jddbv7cyviptqjc7jusxh72uik7zt6adtckl5f4nwy2v72qd.onion/search" class="px-3 py-2 text-xs font-bold text-violet-700 dark:text-violet-400 bg-violet-50 hover:bg-violet-100 dark:bg-violet-950/20 dark:hover:bg-violet-900/30 rounded-xl border border-violet-200 dark:border-violet-800/80 transition text-center">
                                Brave
                            </button>
                            <button type="submit" formaction="https://www.startpage.com/sp/search" class="px-3 py-2 text-xs font-bold text-zinc-700 dark:text-zinc-300 bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-800 dark:hover:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-800/80 transition text-center">
                                Startpage
                            </button>
                            <button type="submit" formaction="https://duckduckgogg42xjoc72x3sjasowoarfbgcmvfimaftt6twagswzczad.onion/lite/" class="px-3 py-2 text-xs font-bold text-violet-700 dark:text-violet-400 bg-violet-50 hover:bg-violet-100 dark:bg-violet-950/20 dark:hover:bg-violet-900/30 rounded-xl border border-violet-200 dark:border-violet-800/80 transition text-center">
                                DuckDuckGo
                            </button>
                            <button type="submit" formaction="https://mwmbl.org/" class="px-3 py-2 text-xs font-bold text-zinc-700 dark:text-zinc-300 bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-800 dark:hover:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-800/80 transition text-center">
                                Mwmbl
                            </button>
                        </div>
                    </form>
                </div>

                {{-- 4get Scraper Portal Card --}}
                <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-sm p-6 space-y-6">
                    <div class="flex items-center gap-2.5 pb-3 border-b border-zinc-100 dark:border-zinc-800">
                        <div class="p-1.5 rounded-lg bg-violet-50 dark:bg-violet-900/30 text-violet-600 dark:text-violet-400">
                            <flux:icon icon="funnel" class="size-4" />
                        </div>
                        <h3 class="text-xs font-bold text-zinc-700 dark:text-zinc-200 uppercase tracking-widest">4get Scraper Gateways</h3>
                    </div>

                    <form method="GET" rel="noreferrer" target="_blank" class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">Search Term</label>
                                <input
                                    type="text"
                                    name="s"
                                    x-model="query"
                                    placeholder="Enter keyword"
                                    required
                                    class="w-full px-3 py-2 rounded-lg bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 text-xs font-mono text-zinc-800 dark:text-zinc-200 focus:outline-none focus:ring-1 focus:ring-violet-500" />
                            </div>

                            <div class="space-y-2">
                                <label class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">Scraper Engine</label>
                                <select name="scraper" class="w-full px-3 py-2 rounded-lg bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 text-xs text-zinc-800 dark:text-zinc-200 focus:outline-none focus:ring-1 focus:ring-violet-500">
                                    <option value="ddg">duckgo</option>
                                    <option value="brave">brave</option>
                                    <option value="yandex" selected>yandex</option>
                                    <option value="google">google</option>
                                    <option value="google_api">google api</option>
                                    <option value="google_cse">google cse</option>
                                    <option value="yahoo_japan">yahoo jp</option>
                                    <option value="startpage">startpage</option>
                                    <option value="qwant">qwant</option>
                                    <option value="ghostery">ghostery</option>
                                    <option value="yep">yep</option>
                                    <option value="greppr">greppr</option>
                                    <option value="crowdview">crowdview</option>
                                    <option value="mwmbl">mwmbl</option>
                                    <option value="mojeek">mojeek</option>
                                    <option value="baidu">baidu</option>
                                    <option value="coccoc">cốc cốc</option>
                                    <option value="solofield">solofield</option>
                                    <option value="marginalia">marginalia</option>
                                    <option value="wiby">wiby</option>
                                    <option value="curlie">curlie</option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-2 pt-2">
                            <button type="submit" formaction="https://4get.sny.sh/web" class="px-3 py-2 text-xs font-bold text-zinc-700 dark:text-zinc-300 bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-800 dark:hover:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-800/80 transition text-center">
                                4get (Primary)
                            </button>
                            <button type="submit" formaction="https://4.nboeck.de/web" class="px-3 py-2 text-xs font-bold text-zinc-700 dark:text-zinc-300 bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-800 dark:hover:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-800/80 transition text-center">
                                4get (Mirror)
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Tor PGP Keyserver & Lyzem Telegram --}}
                <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-sm p-6 space-y-6">
                    <div class="flex items-center gap-2.5 pb-3 border-b border-zinc-100 dark:border-zinc-800">
                        <div class="p-1.5 rounded-lg bg-violet-50 dark:bg-violet-900/30 text-violet-600 dark:text-violet-400">
                            <flux:icon icon="key" class="size-4" />
                        </div>
                        <h3 class="text-xs font-bold text-zinc-700 dark:text-zinc-200 uppercase tracking-widest">PGP & Telegram Search</h3>
                    </div>

                    <div class="space-y-6">
                        {{-- Keyserver --}}
                        <form method="GET" rel="noreferrer" target="_blank" class="space-y-3">
                            <label class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider block">OpenPGP Keyserver ID / Fingerprint</label>
                            <div class="flex gap-2">
                                <input
                                    type="text"
                                    name="q"
                                    x-model="query"
                                    placeholder="Enter Key ID or Fingerprint"
                                    required
                                    class="flex-1 px-3 py-2 rounded-lg bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 text-xs font-mono text-zinc-800 dark:text-zinc-200 focus:outline-none focus:ring-1 focus:ring-violet-500" />
                                <button type="submit" formaction="http://zkaan2xfbuxia2wpf7ofnkbz6r5zdbbvxbunvp5g2iebopbfc4iqmbad.onion/search" class="px-3 py-2 text-xs font-bold text-violet-700 dark:text-violet-400 bg-violet-50 hover:bg-violet-100 dark:bg-violet-950/20 dark:hover:bg-violet-900/30 rounded-xl border border-violet-200 dark:border-violet-800/80 transition flex items-center gap-1">
                                    Onion
                                </button>
                                <button type="submit" formaction="https://keys.openpgp.org/search" class="px-3 py-2 text-xs font-bold text-zinc-700 dark:text-zinc-300 bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-800 dark:hover:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-800/80 transition">
                                    Clearweb
                                </button>
                            </div>
                        </form>

                        {{-- Telegram --}}
                        <form method="GET" rel="noreferrer" target="_blank" class="space-y-3">
                            <input type="hidden" name="f" value="all">
                            <input type="hidden" name="per-page" value="100">
                            <label class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider block">Lyzem Telegram Indexer</label>
                            <div class="flex gap-2">
                                <input
                                    type="text"
                                    name="q"
                                    x-model="query"
                                    placeholder="Enter keyword"
                                    required
                                    class="flex-1 px-3 py-2 rounded-lg bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 text-xs font-mono text-zinc-800 dark:text-zinc-200 focus:outline-none focus:ring-1 focus:ring-violet-500" />
                                <button type="submit" formaction="https://lyzem.com/search" class="px-3 py-2 text-xs font-bold text-zinc-700 dark:text-zinc-300 bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-800 dark:hover:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-800/80 transition">
                                    Search Channels
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Onion Search Engine (The "warn/scam" items) --}}
                <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-sm p-6 space-y-6">
                    <div class="flex items-center gap-2.5 pb-3 border-b border-zinc-100 dark:border-zinc-800">
                        <div class="p-1.5 rounded-lg bg-violet-50 dark:bg-violet-900/30 text-violet-600 dark:text-violet-400">
                            <flux:icon icon="shield-exclamation" class="size-4" />
                        </div>
                        <h3 class="text-xs font-bold text-zinc-700 dark:text-zinc-200 uppercase tracking-widest">Tor Onion Search Indexes</h3>
                    </div>

                    <div class="space-y-4">
                        {{-- Onion Search Form 1 (q query) --}}
                        <form method="GET" rel="noreferrer" target="_blank" class="space-y-3">
                            <label class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider block">Index Query Type A (OnionLand, DeepSearches, Just Onion, VormWeb)</label>
                            <div class="space-y-2">
                                <input
                                    type="text"
                                    name="q"
                                    x-model="query"
                                    placeholder="Enter keyword"
                                    required
                                    class="w-full px-3 py-2 rounded-lg bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 text-xs font-mono text-zinc-800 dark:text-zinc-200 focus:outline-none focus:ring-1 focus:ring-violet-500" />
                                <div class="grid grid-cols-2 gap-2">
                                    <button type="submit" formaction="http://3bbad7fauom4d6sgppalyqddsqbf5u5p56b5k5uk2zxsy3d6ey2jobad.onion/search" class="px-2 py-1.5 text-[11px] font-bold text-violet-700 dark:text-violet-400 bg-violet-50 hover:bg-violet-100 dark:bg-violet-950/20 dark:hover:bg-violet-900/30 rounded-lg border border-violet-200 dark:border-violet-800/80 transition text-center">
                                        OnionLand
                                    </button>
                                    <button type="submit" formaction="http://searchgf7gdtauh7bhnbyed4ivxqmuoat3nm6zfrg3ymkq6mtnpye3ad.onion/search" class="px-2 py-1.5 text-[11px] font-bold text-violet-700 dark:text-violet-400 bg-violet-50 hover:bg-violet-100 dark:bg-violet-950/20 dark:hover:bg-violet-900/30 rounded-lg border border-violet-200 dark:border-violet-800/80 transition text-center">
                                        DeepSearches
                                    </button>
                                    <button type="submit" formaction="http://justdirs5iebdkegiwbp3k6vwgwyr5mce7pztld23hlluy22ox4r3iad.onion/search" class="px-2 py-1.5 text-[11px] font-bold text-violet-700 dark:text-violet-400 bg-violet-50 hover:bg-violet-100 dark:bg-violet-950/20 dark:hover:bg-violet-900/30 rounded-lg border border-violet-200 dark:border-violet-800/80 transition text-center">
                                        Just Onion
                                    </button>
                                    <button type="submit" formaction="http://volkancfgpi4c7ghph6id2t7vcntenuly66qjt6oedwtjmyj4tkk5oqd.onion/search" class="px-2 py-1.5 text-[11px] font-bold text-violet-700 dark:text-violet-400 bg-violet-50 hover:bg-violet-100 dark:bg-violet-950/20 dark:hover:bg-violet-900/30 rounded-lg border border-violet-200 dark:border-violet-800/80 transition text-center">
                                        VormWeb
                                    </button>
                                </div>
                            </div>
                        </form>

                        {{-- Onion Search Form 2 (query query) --}}
                        <form method="GET" rel="noreferrer" target="_blank" class="space-y-3 pt-2 border-t border-zinc-100 dark:border-zinc-800/80">
                            <label class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider block">Index Query Type B (TorDex, Fresh Onions, IMGDex, Light)</label>
                            <div class="space-y-2">
                                <input
                                    type="text"
                                    name="query"
                                    x-model="query"
                                    placeholder="Enter keyword"
                                    required
                                    class="w-full px-3 py-2 rounded-lg bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 text-xs font-mono text-zinc-800 dark:text-zinc-200 focus:outline-none focus:ring-1 focus:ring-violet-500" />
                                <div class="grid grid-cols-2 gap-2">
                                    <button type="submit" formaction="http://tordexu73joywapk2txdr54jed4imqledpcvcuf75qsas2gwdgksvnyd.onion/search" class="px-2 py-1.5 text-[11px] font-bold text-violet-700 dark:text-violet-400 bg-violet-50 hover:bg-violet-100 dark:bg-violet-950/20 dark:hover:bg-violet-900/30 rounded-lg border border-violet-200 dark:border-violet-800/80 transition text-center">
                                        TorDex
                                    </button>
                                    <button type="submit" formaction="http://freshonifyfe4rmuh6qwpsexfhdrww7wnt5qmkoertwxmcuvm4woo4ad.onion/" class="px-2 py-1.5 text-[11px] font-bold text-violet-700 dark:text-violet-400 bg-violet-50 hover:bg-violet-100 dark:bg-violet-950/20 dark:hover:bg-violet-900/30 rounded-lg border border-violet-200 dark:border-violet-800/80 transition text-center">
                                        Fresh Onions
                                    </button>
                                    <button type="submit" formaction="http://tordexu73joywapk2txdr54jed4imqledpcvcuf75qsas2gwdgksvnyd.onion/images" class="px-2 py-1.5 text-[11px] font-bold text-violet-700 dark:text-violet-400 bg-violet-50 hover:bg-violet-100 dark:bg-violet-950/20 dark:hover:bg-violet-900/30 rounded-lg border border-violet-200 dark:border-violet-800/80 transition text-center">
                                        IMGDex
                                    </button>
                                    <button type="submit" formaction="http://light3232dmbbnigk34aeg2ef3j3uvnwkqsymunadh3to3vg4gpyeyid.onion/search" class="px-2 py-1.5 text-[11px] font-bold text-violet-700 dark:text-violet-400 bg-violet-50 hover:bg-violet-100 dark:bg-violet-950/20 dark:hover:bg-violet-900/30 rounded-lg border border-violet-200 dark:border-violet-800/80 transition text-center">
                                        Light Search
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>

        {{-- ==================== TAB 2: TRANSLATION & REFERENCE ==================== --}}
        <div x-show="activeTab === 'translation'" class="grid grid-cols-1 md:grid-cols-2 gap-6" x-cloak>

            {{-- Google Translate Card --}}
            <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-sm p-6 space-y-6">
                <div class="flex items-center gap-2.5 pb-3 border-b border-zinc-100 dark:border-zinc-800">
                    <div class="p-1.5 rounded-lg bg-violet-50 dark:bg-violet-900/30 text-violet-600 dark:text-violet-400">
                        <flux:icon icon="language" class="size-4" />
                    </div>
                    <h3 class="text-xs font-bold text-zinc-700 dark:text-zinc-200 uppercase tracking-widest">Google Translate (Mobile Portal)</h3>
                </div>

                <form method="GET" rel="noreferrer" target="_blank" class="space-y-4">
                    <input type="hidden" name="hl" value="en-US">

                    <div class="grid grid-cols-2 gap-3">
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider block">Source Lang</label>
                            <select name="sl" class="w-full px-3 py-1.5 rounded-lg bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 text-xs text-zinc-800 dark:text-zinc-200 focus:outline-none focus:ring-1 focus:ring-violet-500">
                                <option value="auto" selected>Autodetect</option>
                                <option value="en">English</option>
                            </select>
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider block">Target Lang</label>
                            <select name="tl" class="w-full px-3 py-1.5 rounded-lg bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 text-xs text-zinc-800 dark:text-zinc-200 focus:outline-none focus:ring-1 focus:ring-violet-500">
                                <option value="en" selected>English</option>
                                <option value="af">Afrikaans</option>
                                <option value="sq">Albanian</option>
                                <option value="am">Amharic</option>
                                <option value="ar">Arabic</option>
                                <option value="hy">Armenian</option>
                                <option value="az">Azerbaijani</option>
                                <option value="zh-CN">Chinese (Simplified)</option>
                                <option value="zh-TW">Chinese (Traditional)</option>
                                <option value="fr">French</option>
                                <option value="de">German</option>
                                <option value="el">Greek</option>
                                <option value="iw">Hebrew</option>
                                <option value="hi">Hindi</option>
                                <option value="id">Indonesian</option>
                                <option value="it">Italian</option>
                                <option value="ja">Japanese</option>
                                <option value="ko">Korean</option>
                                <option value="la">Latin</option>
                                <option value="pl">Polish</option>
                                <option value="pt">Portuguese</option>
                                <option value="ro">Romanian</option>
                                <option value="ru">Russian</option>
                                <option value="es">Spanish</option>
                                <option value="th">Thai</option>
                                <option value="tr">Turkish</option>
                                <option value="uk">Ukrainian</option>
                                <option value="vi">Vietnamese</option>
                            </select>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider block">Translation Text</label>
                        <textarea
                            name="q"
                            x-model="query"
                            placeholder="Enter text to translate"
                            required
                            maxlength="5000"
                            rows="3"
                            class="w-full px-3 py-2 rounded-lg bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 text-xs font-mono text-zinc-800 dark:text-zinc-200 focus:outline-none focus:ring-1 focus:ring-violet-500 resize-y"></textarea>
                    </div>

                    <button type="submit" formaction="https://translate.google.com/m" class="w-full px-4 py-2 bg-violet-600 hover:bg-violet-700 text-white font-bold rounded-xl text-xs shadow transition text-center">
                        Open Google Translate
                    </button>
                </form>
            </div>

            {{-- SimplyTranslate Card --}}
            <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-sm p-6 space-y-6">
                <div class="flex items-center gap-2.5 pb-3 border-b border-zinc-100 dark:border-zinc-800">
                    <div class="p-1.5 rounded-lg bg-violet-50 dark:bg-violet-900/30 text-violet-600 dark:text-violet-400">
                        <flux:icon icon="bolt" class="size-4" />
                    </div>
                    <h3 class="text-xs font-bold text-zinc-700 dark:text-zinc-200 uppercase tracking-widest">SimplyTranslate (Privacy Mirrors)</h3>
                </div>

                <form method="POST" rel="noreferrer" target="_blank" class="space-y-4">
                    <div class="grid grid-cols-2 gap-3">
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider block">Source Lang</label>
                            <select name="from_language" class="w-full px-3 py-1.5 rounded-lg bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 text-xs text-zinc-800 dark:text-zinc-200 focus:outline-none focus:ring-1 focus:ring-violet-500">
                                <option value="Autodetect" selected>Autodetect</option>
                                <option value="English">English</option>
                            </select>
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider block">Target Lang</label>
                            <select name="to_language" class="w-full px-3 py-1.5 rounded-lg bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 text-xs text-zinc-800 dark:text-zinc-200 focus:outline-none focus:ring-1 focus:ring-violet-500">
                                <option value="English" selected>English</option>
                                <option value="French">French</option>
                                <option value="German">German</option>
                                <option value="Spanish">Spanish</option>
                                <option value="Chinese (Simplified)">Chinese simpl.</option>
                                <option value="Chinese (Traditional)">Chinese trad.</option>
                                <option value="Indonesian">Indonesian</option>
                                <option value="Russian">Russian</option>
                                <option value="Japanese">Japanese</option>
                            </select>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider block">Translation Text</label>
                        <textarea
                            name="input"
                            x-model="query"
                            placeholder="Enter text to translate via SimplyTranslate"
                            required
                            maxlength="5000"
                            rows="3"
                            class="w-full px-3 py-2 rounded-lg bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 text-xs font-mono text-zinc-800 dark:text-zinc-200 focus:outline-none focus:ring-1 focus:ring-violet-500 resize-y"></textarea>
                    </div>

                    <div class="grid grid-cols-3 gap-2">
                        <button type="submit" formaction="http://st.g4c3eya4clenolymqbpgwz3q3tawoxw56yhzk4vugqrl6dtu3ejvhjid.onion/?engine=google" class="px-2 py-2 text-[10px] font-bold text-violet-700 dark:text-violet-400 bg-violet-50 hover:bg-violet-100 dark:bg-violet-950/20 dark:hover:bg-violet-900/30 rounded-xl border border-violet-200 dark:border-violet-800/80 transition text-center">
                            Onion
                        </button>
                        <button type="submit" formaction="https://simplytranslate.reallyaweso.me/?engine=google" class="px-2 py-2 text-[10px] font-bold text-zinc-700 dark:text-zinc-300 bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-800 dark:hover:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-800/80 transition text-center">
                            Mirror A
                        </button>
                        <button type="submit" formaction="https://simplytranslate.leemoon.network/?engine=google" class="px-2 py-2 text-[10px] font-bold text-zinc-700 dark:text-zinc-300 bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-800 dark:hover:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-800/80 transition text-center">
                            Mirror B
                        </button>
                    </div>
                </form>
            </div>

            {{-- English Utilities Card --}}
            <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-sm p-6 space-y-6">
                <div class="flex items-center gap-2.5 pb-3 border-b border-zinc-100 dark:border-zinc-800">
                    <div class="p-1.5 rounded-lg bg-violet-50 dark:bg-violet-900/30 text-violet-600 dark:text-violet-400">
                        <flux:icon icon="pencil" class="size-4" />
                    </div>
                    <h3 class="text-xs font-bold text-zinc-700 dark:text-zinc-200 uppercase tracking-widest">English Vocabulary & Grammars</h3>
                </div>

                <div class="space-y-4">
                    {{-- FrazeIt --}}
                    <form action="https://fraze.it/n_search.jsp" method="GET" rel="noreferrer" target="_blank" class="flex gap-2 items-center">
                        <div class="flex-1">
                            <input
                                type="text"
                                name="q"
                                x-model="query"
                                placeholder="Sentence context lookup..."
                                required
                                class="w-full px-3 py-2 rounded-lg bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 text-xs font-mono text-zinc-800 dark:text-zinc-200 focus:outline-none focus:ring-1 focus:ring-violet-500" />
                        </div>
                        <button type="submit" class="px-3 py-2 text-xs font-bold text-zinc-750 dark:text-zinc-200 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700/80 rounded-xl border border-zinc-250 dark:border-zinc-700 transition">
                            FrazeIt
                        </button>
                    </form>

                    {{-- Wordnik --}}
                    <form action="https://www.wordnik.com/words" method="GET" rel="noreferrer" target="_blank" class="flex gap-2 items-center">
                        <input type="hidden" name="x" value="0">
                        <input type="hidden" name="y" value="0">
                        <div class="flex-1">
                            <input
                                type="text"
                                name="myWord"
                                x-model="query"
                                placeholder="Word definitions & relations..."
                                required
                                class="w-full px-3 py-2 rounded-lg bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 text-xs font-mono text-zinc-800 dark:text-zinc-200 focus:outline-none focus:ring-1 focus:ring-violet-500" />
                        </div>
                        <button type="submit" class="px-3 py-2 text-xs font-bold text-zinc-750 dark:text-zinc-200 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700/80 rounded-xl border border-zinc-250 dark:border-zinc-700 transition">
                            Wordnik
                        </button>
                    </form>

                    {{-- Cambridge Dictionary --}}
                    <form action="https://dictionary.cambridge.org/search/direct/" method="GET" rel="noreferrer" target="_blank" class="flex gap-2 items-center">
                        <input type="hidden" name="datasetsearch" value="english">
                        <div class="flex-1">
                            <input
                                type="text"
                                name="q"
                                x-model="query"
                                placeholder="Cambridge word search..."
                                required
                                class="w-full px-3 py-2 rounded-lg bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 text-xs font-mono text-zinc-800 dark:text-zinc-200 focus:outline-none focus:ring-1 focus:ring-violet-500" />
                        </div>
                        <button type="submit" class="px-3 py-2 text-xs font-bold text-zinc-750 dark:text-zinc-200 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700/80 rounded-xl border border-zinc-250 dark:border-zinc-700 transition">
                            Cambridge
                        </button>
                    </form>

                    {{-- WordHippo Synonyms --}}
                    <form action="https://www.wordhippo.com/what-is/process-form.html" method="POST" rel="noreferrer" target="_blank" class="flex gap-2 items-center">
                        <input type="hidden" name="action" value="synonyms">
                        <div class="flex-1">
                            <input
                                type="text"
                                name="word"
                                x-model="query"
                                placeholder="WordHippo thesaurus search..."
                                required
                                class="w-full px-3 py-2 rounded-lg bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 text-xs font-mono text-zinc-800 dark:text-zinc-200 focus:outline-none focus:ring-1 focus:ring-violet-500" />
                        </div>
                        <button type="submit" class="px-3 py-2 text-xs font-bold text-zinc-750 dark:text-zinc-200 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700/80 rounded-xl border border-zinc-250 dark:border-zinc-700 transition">
                            WordHippo
                        </button>
                    </form>
                </div>
            </div>

            {{-- Wikipedia & Reference Card --}}
            <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-sm p-6 space-y-6">
                <div class="flex items-center gap-2.5 pb-3 border-b border-zinc-100 dark:border-zinc-800">
                    <div class="p-1.5 rounded-lg bg-violet-50 dark:bg-violet-900/30 text-violet-600 dark:text-violet-400">
                        <flux:icon icon="academic-cap" class="size-4" />
                    </div>
                    <h3 class="text-xs font-bold text-zinc-700 dark:text-zinc-200 uppercase tracking-widest">Encyclopedia Gateways</h3>
                </div>

                <form method="GET" rel="noreferrer" target="_blank" class="space-y-4">
                    <input type="hidden" name="lang" value="en">
                    <input type="hidden" name="title" value="Special:Search">
                    <input type="hidden" name="fulltext" value="Search">

                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider block">Wikipedia Search</label>
                        <input
                            type="text"
                            name="search"
                            x-model="query"
                            placeholder="Enter keyword"
                            required
                            class="w-full px-3 py-2 rounded-lg bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 text-xs font-mono text-zinc-800 dark:text-zinc-200 focus:outline-none focus:ring-1 focus:ring-violet-500" />
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <button type="submit" formaction="https://wikipedia.org/w/index.php" class="px-3 py-2 text-xs font-bold text-zinc-700 dark:text-zinc-300 bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-800 dark:hover:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-800/80 transition text-center">
                            Wikipedia
                        </button>
                        <button type="submit" formaction="http://ybgg2evrcdz37y2qes23ff3wjqjdn33tthgoagi76vhxytu4mpxiz5qd.onion/w/index.php" class="px-3 py-2 text-xs font-bold text-violet-700 dark:text-violet-400 bg-violet-50 hover:bg-violet-100 dark:bg-violet-950/20 dark:hover:bg-violet-900/30 rounded-xl border border-violet-200 dark:border-violet-800/80 transition text-center">
                            Wikiless
                        </button>
                    </div>
                </form>
            </div>

        </div>

        {{-- ==================== TAB 3: SOCIALS & MEDIA ==================== --}}
        <div x-show="activeTab === 'socials'" class="grid grid-cols-1 md:grid-cols-2 gap-6" x-cloak>

            {{-- Reddit & Redlib Card --}}
            <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-sm p-6 space-y-6">
                <div class="flex items-center gap-2.5 pb-3 border-b border-zinc-100 dark:border-zinc-800">
                    <div class="p-1.5 rounded-lg bg-violet-50 dark:bg-violet-900/30 text-violet-600 dark:text-violet-400">
                        <flux:icon icon="chat-bubble-bottom-center-text" class="size-4" />
                    </div>
                    <h3 class="text-xs font-bold text-zinc-700 dark:text-zinc-200 uppercase tracking-widest">Reddit & Redlib Frontends</h3>
                </div>

                <form method="GET" rel="noreferrer" target="_blank" class="space-y-4">
                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider block">Search Query</label>
                        <input
                            type="text"
                            name="q"
                            x-model="query"
                            placeholder="Enter reddit keyword"
                            required
                            class="w-full px-3 py-2 rounded-lg bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 text-xs font-mono text-zinc-800 dark:text-zinc-200 focus:outline-none focus:ring-1 focus:ring-violet-500" />
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider block font-semibold">Sorted By</label>
                            <select name="sort" class="w-full px-3 py-1.5 rounded-lg bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 text-xs text-zinc-800 dark:text-zinc-200 focus:outline-none focus:ring-1 focus:ring-violet-500">
                                <option value="relevance" selected>relevance</option>
                                <option value="hot">hot</option>
                                <option value="top">top</option>
                                <option value="new">new</option>
                                <option value="comments">comments</option>
                            </select>
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider block font-semibold">Links From</label>
                            <select name="t" class="w-full px-3 py-1.5 rounded-lg bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 text-xs text-zinc-800 dark:text-zinc-200 focus:outline-none focus:ring-1 focus:ring-violet-500">
                                <option value="hour">1 hour</option>
                                <option value="day">1 day</option>
                                <option value="week">1 week</option>
                                <option value="month">1 month</option>
                                <option value="year">1 year</option>
                                <option value="all" selected>all time</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2 pt-2">
                        <button type="submit" formaction="https://old.reddittorjg6rue252oqsxryoxengawnmo46qy4kyii5wtqnwfj4ooad.onion/search" class="px-2 py-2 text-[11px] font-bold text-violet-700 dark:text-violet-400 bg-violet-50 hover:bg-violet-100 dark:bg-violet-950/20 dark:hover:bg-violet-900/30 rounded-xl border border-violet-200 dark:border-violet-800/80 transition text-center">
                            OldReddit
                        </button>
                        <button type="submit" formaction="http://red.lpoaj7z2zkajuhgnlltpeqh3zyq7wk2iyeggqaduhgxhyajtdt2j7wad.onion/search" class="px-2 py-2 text-[11px] font-bold text-violet-700 dark:text-violet-400 bg-violet-50 hover:bg-violet-100 dark:bg-violet-950/20 dark:hover:bg-violet-900/30 rounded-xl border border-violet-200 dark:border-violet-800/80 transition text-center">
                            Redlib Onion
                        </button>
                        <button type="submit" formaction="http://redlib.catsarchywsyuss6jdxlypsw5dc7owd5u5tr6bujxb7o6xw2hipqehyd.onion/search" class="px-2 py-2 text-[11px] font-bold text-zinc-700 dark:text-zinc-300 bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-800 dark:hover:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-800/80 transition text-center">
                            Redlib Mirror A
                        </button>
                        <button type="submit" formaction="https://redlib.perennialte.ch/search" class="px-2 py-2 text-[11px] font-bold text-zinc-700 dark:text-zinc-300 bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-800 dark:hover:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-800/80 transition text-center">
                            Redlib Mirror B
                        </button>
                    </div>
                </form>
            </div>

            {{-- Pinterest (Binternet) Card --}}
            <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-sm p-6 space-y-6">
                <div class="flex items-center gap-2.5 pb-3 border-b border-zinc-100 dark:border-zinc-800">
                    <div class="p-1.5 rounded-lg bg-violet-50 dark:bg-violet-900/30 text-violet-600 dark:text-violet-400">
                        <flux:icon icon="photo" class="size-4" />
                    </div>
                    <h3 class="text-xs font-bold text-zinc-700 dark:text-zinc-200 uppercase tracking-widest">Pinterest (Binternet Privacy Proxies)</h3>
                </div>

                <form method="GET" rel="noreferrer" target="_blank" class="space-y-4">
                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider block">Image Keyword</label>
                        <input
                            type="text"
                            name="q"
                            x-model="query"
                            placeholder="Enter keyword"
                            required
                            class="w-full px-3 py-2 rounded-lg bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 text-xs font-mono text-zinc-800 dark:text-zinc-200 focus:outline-none focus:ring-1 focus:ring-violet-500" />
                    </div>

                    <div class="grid grid-cols-3 gap-2">
                        <button type="submit" formaction="http://binternet.privacyrkwfzsfmwtfnrilikxv7xkhw2feso7stq2ajmc6wx43hgj6ad.onion/search.php" class="px-2 py-2 text-[10px] font-bold text-violet-700 dark:text-violet-400 bg-violet-50 hover:bg-violet-100 dark:bg-violet-950/20 dark:hover:bg-violet-900/30 rounded-xl border border-violet-200 dark:border-violet-800/80 transition text-center">
                            Onion
                        </button>
                        <button type="submit" formaction="https://binternet.ducks.party/search.php" class="px-2 py-2 text-[10px] font-bold text-zinc-700 dark:text-zinc-300 bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-800 dark:hover:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-800/80 transition text-center">
                            Mirror A
                        </button>
                        <button type="submit" formaction="https://bn.opnxng.com/search.php" class="px-2 py-2 text-[10px] font-bold text-zinc-700 dark:text-zinc-300 bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-800 dark:hover:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-800/80 transition text-center">
                            Mirror B
                        </button>
                    </div>
                </form>
            </div>

            {{-- Twitter (Nitter) Card --}}
            <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-sm p-6 space-y-6">
                <div class="flex items-center gap-2.5 pb-3 border-b border-zinc-100 dark:border-zinc-800">
                    <div class="p-1.5 rounded-lg bg-violet-50 dark:bg-violet-900/30 text-violet-600 dark:text-violet-400">
                        <flux:icon icon="hashtag" class="size-4" />
                    </div>
                    <h3 class="text-xs font-bold text-zinc-700 dark:text-zinc-200 uppercase tracking-widest">Twitter (Nitter Onion Portal)</h3>
                </div>

                <form method="GET" rel="noreferrer" target="_blank" class="space-y-4">
                    <input type="hidden" name="f" value="tweets">

                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider block">Tweets Keyword</label>
                        <input
                            type="text"
                            name="q"
                            x-model="query"
                            placeholder="Enter tweets query"
                            required
                            class="w-full px-3 py-2 rounded-lg bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 text-xs font-mono text-zinc-800 dark:text-zinc-200 focus:outline-none focus:ring-1 focus:ring-violet-500" />
                    </div>

                    <button type="submit" formaction="http://nitter.catsarchywsyuss6jdxlypsw5dc7owd5u5tr6bujxb7o6xw2hipqehyd.onion/search" class="w-full px-3 py-2 text-xs font-bold text-violet-700 dark:text-violet-400 bg-violet-50 hover:bg-violet-100 dark:bg-violet-950/20 dark:hover:bg-violet-900/30 rounded-xl border border-violet-200 dark:border-violet-800/80 transition text-center">
                        Search Nitter Onion
                    </button>
                </form>
            </div>

            {{-- Imgur & Rimgo Card --}}
            <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-sm p-6 space-y-6">
                <div class="flex items-center gap-2.5 pb-3 border-b border-zinc-100 dark:border-zinc-800">
                    <div class="p-1.5 rounded-lg bg-violet-50 dark:bg-violet-900/30 text-violet-600 dark:text-violet-400">
                        <flux:icon icon="photo" class="size-4" />
                    </div>
                    <h3 class="text-xs font-bold text-zinc-700 dark:text-zinc-200 uppercase tracking-widest">Imgur & Rimgo (Onion Proxies)</h3>
                </div>

                <form method="GET" rel="noreferrer" target="_blank" class="space-y-4">
                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider block">Image URL / Keyword</label>
                        <input
                            type="text"
                            name="q"
                            x-model="query"
                            placeholder="Enter keyword or url"
                            required
                            class="w-full px-3 py-2 rounded-lg bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 text-xs font-mono text-zinc-800 dark:text-zinc-200 focus:outline-none focus:ring-1 focus:ring-violet-500" />
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <button type="submit" formaction="http://imgurolljpstjc2w5gb6lgcxhvdsndmurvbbkvpv6s5pif3kkhe5ptad.onion/search" class="px-2 py-2 text-[11px] font-bold text-violet-700 dark:text-violet-400 bg-violet-50 hover:bg-violet-100 dark:bg-violet-950/20 dark:hover:bg-violet-900/30 rounded-xl border border-violet-200 dark:border-violet-800/80 transition text-center">
                            Imgur Onion
                        </button>
                        <button type="submit" formaction="http://rimgo.catsarchywsyuss6jdxlypsw5dc7owd5u5tr6bujxb7o6xw2hipqehyd.onion/search" class="px-2 py-2 text-[11px] font-bold text-violet-700 dark:text-violet-400 bg-violet-50 hover:bg-violet-100 dark:bg-violet-950/20 dark:hover:bg-violet-900/30 rounded-xl border border-violet-200 dark:border-violet-800/80 transition text-center">
                            Rimgo Onion
                        </button>
                    </div>
                </form>
            </div>




        </div>

        {{-- ==================== TAB 4: LIBRARIES & CRYPTO ==================== --}}
        <div x-show="activeTab === 'libraries'" class="grid grid-cols-1 md:grid-cols-2 gap-6" x-cloak>

            {{-- Sci-Hub Gateway --}}
            <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-sm p-6 space-y-6">
                <div class="flex items-center gap-2.5 pb-3 border-b border-zinc-100 dark:border-zinc-800">
                    <div class="p-1.5 rounded-lg bg-violet-50 dark:bg-violet-900/30 text-violet-600 dark:text-violet-400">
                        <flux:icon icon="academic-cap" class="size-4" />
                    </div>
                    <h3 class="text-xs font-bold text-zinc-700 dark:text-zinc-200 uppercase tracking-widest">Sci-Hub Scientific Papers</h3>
                </div>

                <form method="POST" rel="noreferrer" target="_blank" class="space-y-4">
                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider block">DOI, URL or Title Reference</label>
                        <input
                            type="text"
                            name="request"
                            x-model="query"
                            placeholder="Enter reference link"
                            required
                            class="w-full px-3 py-2 rounded-lg bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 text-xs font-mono text-zinc-800 dark:text-zinc-200 focus:outline-none focus:ring-1 focus:ring-violet-500" />
                    </div>

                    <div class="grid grid-cols-3 gap-2">
                        <button type="submit" formaction="https://sci-hub.ru/" class="px-2 py-2 text-[10px] font-bold text-zinc-700 dark:text-zinc-300 bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-800 dark:hover:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-800/80 transition text-center">
                            sci-hub.ru
                        </button>
                        <button type="submit" formaction="https://sci-hub.st/" class="px-2 py-2 text-[10px] font-bold text-zinc-700 dark:text-zinc-300 bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-800 dark:hover:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-800/80 transition text-center">
                            sci-hub.st
                        </button>
                        <button type="submit" formaction="https://sci-hub.red/" class="px-2 py-2 text-[10px] font-bold text-zinc-700 dark:text-zinc-300 bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-800 dark:hover:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-800/80 transition text-center">
                            sci-hub.red
                        </button>
                    </div>
                </form>
            </div>

            {{-- Anna's Archive Gateway --}}
            <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-sm p-6 space-y-6">
                <div class="flex items-center gap-2.5 pb-3 border-b border-zinc-100 dark:border-zinc-800">
                    <div class="p-1.5 rounded-lg bg-violet-50 dark:bg-violet-900/30 text-violet-600 dark:text-violet-400">
                        <flux:icon icon="book-open" class="size-4" />
                    </div>
                    <h3 class="text-xs font-bold text-zinc-700 dark:text-zinc-200 uppercase tracking-widest">Anna’s Archive (E-Books & Papers)</h3>
                </div>

                <form method="GET" rel="noreferrer" target="_blank" class="space-y-4">
                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider block">Book Title / Author / ISBN</label>
                        <input
                            type="text"
                            name="q"
                            x-model="query"
                            placeholder="Enter keyword"
                            required
                            class="w-full px-3 py-2 rounded-lg bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 text-xs font-mono text-zinc-800 dark:text-zinc-200 focus:outline-none focus:ring-1 focus:ring-violet-500" />
                    </div>

                    <div class="grid grid-cols-3 gap-2">
                        <button type="submit" formaction="https://annas-archive.pk/search" class="px-2 py-2 text-[10px] font-bold text-zinc-700 dark:text-zinc-300 bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-800 dark:hover:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-800/80 transition text-center">
                            annas.pk
                        </button>
                        <button type="submit" formaction="https://annas-archive.gl/search" class="px-2 py-2 text-[10px] font-bold text-zinc-700 dark:text-zinc-300 bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-800 dark:hover:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-800/80 transition text-center">
                            annas.gl
                        </button>
                        <button type="submit" formaction="https://annas-archive.gd/search" class="px-2 py-2 text-[10px] font-bold text-zinc-700 dark:text-zinc-300 bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-800 dark:hover:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-800/80 transition text-center">
                            annas.gd
                        </button>
                    </div>
                </form>
            </div>

            {{-- Library Genesis --}}
            <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-sm p-6 space-y-6">
                <div class="flex items-center gap-2.5 pb-3 border-b border-zinc-100 dark:border-zinc-800">
                    <div class="p-1.5 rounded-lg bg-violet-50 dark:bg-violet-900/30 text-violet-600 dark:text-violet-400">
                        <flux:icon icon="bookmark" class="size-4" />
                    </div>
                    <h3 class="text-xs font-bold text-zinc-700 dark:text-zinc-200 uppercase tracking-widest">Library Genesis (Libgen)</h3>
                </div>

                <form method="GET" rel="noreferrer" target="_blank" class="space-y-4">
                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider block">Book Reference</label>
                        <input
                            type="text"
                            name="req"
                            x-model="query"
                            placeholder="Enter keyword"
                            required
                            class="w-full px-3 py-2 rounded-lg bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 text-xs font-mono text-zinc-800 dark:text-zinc-200 focus:outline-none focus:ring-1 focus:ring-violet-500" />
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <button type="submit" formaction="https://libgen.li/index.php" class="px-3 py-2 text-xs font-bold text-zinc-700 dark:text-zinc-300 bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-800 dark:hover:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-800/80 transition text-center">
                            libgen.li
                        </button>
                        <button type="submit" formaction="https://libgen.vg/index.php" class="px-3 py-2 text-xs font-bold text-zinc-700 dark:text-zinc-300 bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-800 dark:hover:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-800/80 transition text-center">
                            libgen.vg
                        </button>
                    </div>
                </form>
            </div>

            {{-- Bitcoin Explorer --}}
            <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-sm p-6 space-y-6">
                <div class="flex items-center gap-2.5 pb-3 border-b border-zinc-100 dark:border-zinc-800">
                    <div class="p-1.5 rounded-lg bg-violet-50 dark:bg-violet-900/30 text-violet-600 dark:text-violet-400">
                        <flux:icon icon="circle-stack" class="size-4" />
                    </div>
                    <h3 class="text-xs font-bold text-zinc-700 dark:text-zinc-200 uppercase tracking-widest">Bitcoin address & transaction explorer</h3>
                </div>

                <form method="GET" rel="noreferrer" target="_blank" class="space-y-4">
                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider block">BTC Address / Block / Tx Hash</label>
                        <input
                            type="text"
                            name="q"
                            x-model="query"
                            placeholder="bitcoin address"
                            required
                            class="w-full px-3 py-2 rounded-lg bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 text-xs font-mono text-zinc-800 dark:text-zinc-200 focus:outline-none focus:ring-1 focus:ring-violet-500" />
                    </div>

                    <div class="grid grid-cols-3 gap-2">
                        <button type="submit" formaction="http://3xplor3rzajysy4j5fi3g3k27vivfcw75zjxdb2tg2wpz3i4cdiyhxyd.onion/search" class="px-2 py-2 text-[10px] font-bold text-violet-700 dark:text-violet-400 bg-violet-50 hover:bg-violet-100 dark:bg-violet-950/20 dark:hover:bg-violet-900/30 rounded-xl border border-violet-200 dark:border-violet-800/80 transition text-center">
                            3xpl
                        </button>
                        <button type="submit" formaction="http://blkchairbknpn73cfjhevhla7rkp4ed5gg2knctvv7it4lioy22defid.onion/search" class="px-2 py-2 text-[10px] font-bold text-violet-700 dark:text-violet-400 bg-violet-50 hover:bg-violet-100 dark:bg-violet-950/20 dark:hover:bg-violet-900/30 rounded-xl border border-violet-200 dark:border-violet-800/80 transition text-center">
                            Blockchair
                        </button>
                        <button type="submit" formaction="https://btcscan.org/nojs/search" class="px-2 py-2 text-[10px] font-bold text-zinc-700 dark:text-zinc-300 bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-800 dark:hover:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-800/80 transition text-center">
                            BTCScan
                        </button>
                    </div>
                </form>
            </div>

            {{-- Monero Explorer --}}
            <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-sm p-6 space-y-6">
                <div class="flex items-center gap-2.5 pb-3 border-b border-zinc-100 dark:border-zinc-800">
                    <div class="p-1.5 rounded-lg bg-violet-50 dark:bg-violet-900/30 text-violet-600 dark:text-violet-400">
                        <flux:icon icon="circle-stack" class="size-4" />
                    </div>
                    <h3 class="text-xs font-bold text-zinc-700 dark:text-zinc-200 uppercase tracking-widest">Monero (XMR) Privacy Explorer</h3>
                </div>

                <form method="GET" rel="noreferrer" target="_blank" class="space-y-4">
                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider block">XMR Transaction Key / Address</label>
                        <input
                            type="text"
                            name="value"
                            x-model="query"
                            placeholder="monero transaction"
                            required
                            class="w-full px-3 py-2 rounded-lg bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 text-xs font-mono text-zinc-800 dark:text-zinc-200 focus:outline-none focus:ring-1 focus:ring-violet-500" />
                    </div>

                    <button type="submit" formaction="http://ol7qm5adjeugpwkbrcnnnshsihmkhidaaoim35duhfdmj4gihaiapkid.onion/search" class="w-full px-3 py-2 text-xs font-bold text-violet-700 dark:text-violet-400 bg-violet-50 hover:bg-violet-100 dark:bg-violet-950/20 dark:hover:bg-violet-900/30 rounded-xl border border-violet-200 dark:border-violet-800/80 transition text-center">
                        Search Xmrchain (Onion)
                    </button>
                </form>
            </div>

        </div>

        {{-- ==================== TAB 5: ANIME & BOORUS ==================== --}}
        <div x-show="activeTab === 'boorus'" class="grid grid-cols-1 md:grid-cols-2 gap-6" x-cloak>

            {{-- GET Boorus (Yandere, Konachan, Sakugabooru, TPB) --}}
            <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-sm p-6 space-y-6">
                <div class="flex items-center gap-2.5 pb-3 border-b border-zinc-100 dark:border-zinc-800">
                    <div class="p-1.5 rounded-lg bg-violet-50 dark:bg-violet-900/30 text-violet-600 dark:text-violet-400">
                        <flux:icon icon="photo" class="size-4" />
                    </div>
                    <h3 class="text-xs font-bold text-zinc-700 dark:text-zinc-200 uppercase tracking-widest">Imageboards (GET Curation Index)</h3>
                </div>

                <form method="GET" rel="noreferrer" target="_blank" class="space-y-4">
                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider block">Tags (space separated)</label>
                        <input
                            type="text"
                            name="tags"
                            x-model="query"
                            placeholder="e.g. 1girl solo"
                            required
                            class="w-full px-3 py-2 rounded-lg bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 text-xs font-mono text-zinc-800 dark:text-zinc-200 focus:outline-none focus:ring-1 focus:ring-violet-500" />
                    </div>

                    <div class="grid grid-cols-2 gap-2 pt-2">
                        <button type="submit" formaction="https://yande.re/post" class="px-2 py-2 text-xs font-bold text-zinc-700 dark:text-zinc-300 bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-800 dark:hover:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-800/80 transition text-center">
                            Yandere
                        </button>
                        <button type="submit" formaction="https://konachan.net/post" class="px-2 py-2 text-xs font-bold text-zinc-700 dark:text-zinc-300 bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-800 dark:hover:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-800/80 transition text-center">
                            Konachan
                        </button>
                        <button type="submit" formaction="https://sakugabooru.com/post" class="px-2 py-2 text-xs font-bold text-zinc-700 dark:text-zinc-300 bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-800 dark:hover:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-800/80 transition text-center">
                            Sakugabooru
                        </button>
                        <button type="submit" formaction="http://owmvhpxyisu6fgd7r2fcswgavs7jly4znldaey33utadwmgbbp4pysad.onion/posts/" class="px-2 py-2 text-xs font-bold text-violet-700 dark:text-violet-400 bg-violet-50 hover:bg-violet-100 dark:bg-violet-950/20 dark:hover:bg-violet-900/30 rounded-xl border border-violet-200 dark:border-violet-800/80 transition text-center">
                            TPB Onion
                        </button>
                    </div>
                </form>
            </div>

            {{-- POST Boorus (Gelbooru, TBIB, Safebooru) --}}
            <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-sm p-6 space-y-6">
                <div class="flex items-center gap-2.5 pb-3 border-b border-zinc-100 dark:border-zinc-800">
                    <div class="p-1.5 rounded-lg bg-violet-50 dark:bg-violet-900/30 text-violet-600 dark:text-violet-400">
                        <flux:icon icon="photo" class="size-4" />
                    </div>
                    <h3 class="text-xs font-bold text-zinc-700 dark:text-zinc-200 uppercase tracking-widest">Imageboards (POST Curation Index)</h3>
                </div>

                <form method="POST" rel="noreferrer" target="_blank" class="space-y-4">
                    <input type="hidden" name="commit" value="Search">

                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider block">Tags (space separated)</label>
                        <input
                            type="text"
                            name="tags"
                            x-model="query"
                            placeholder="e.g. 1girl solo"
                            required
                            class="w-full px-3 py-2 rounded-lg bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 text-xs font-mono text-zinc-800 dark:text-zinc-200 focus:outline-none focus:ring-1 focus:ring-violet-500" />
                    </div>

                    <div class="grid grid-cols-3 gap-2">
                        <button type="submit" formaction="https://gelbooru.com/index.php?page=search" class="px-1.5 py-2 text-[11px] font-bold text-zinc-700 dark:text-zinc-300 bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-800 dark:hover:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-800/80 transition text-center">
                            Gelbooru
                        </button>
                        <button type="submit" formaction="https://tbib.org/index.php?page=search" class="px-1.5 py-2 text-[11px] font-bold text-zinc-700 dark:text-zinc-300 bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-800 dark:hover:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-800/80 transition text-center">
                            TBIB (Safe)
                        </button>
                        <button type="submit" formaction="https://safebooru.org/index.php?page=search" class="px-1.5 py-2 text-[11px] font-bold text-zinc-700 dark:text-zinc-300 bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-800 dark:hover:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-800/80 transition text-center">
                            Safebooru
                        </button>
                    </div>
                </form>
            </div>

            {{-- Anime-pictures --}}
            <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-sm p-6 space-y-6">
                <div class="flex items-center gap-2.5 pb-3 border-b border-zinc-100 dark:border-zinc-800">
                    <div class="p-1.5 rounded-lg bg-violet-50 dark:bg-violet-900/30 text-violet-600 dark:text-violet-400">
                        <flux:icon icon="photo" class="size-4" />
                    </div>
                    <h3 class="text-xs font-bold text-zinc-700 dark:text-zinc-200 uppercase tracking-widest">Anime-Pictures Gallery</h3>
                </div>

                <form action="https://anime-pictures.net/posts" method="GET" rel="noreferrer" target="_blank" class="space-y-4">
                    <input type="hidden" name="lang" value="en">

                    <div class="space-y-2">
                        <label class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider block">Tag</label>
                        <input
                            type="text"
                            name="search_tag"
                            x-model="query"
                            placeholder="girl"
                            required
                            class="w-full px-3 py-2 rounded-lg bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 text-xs font-mono text-zinc-800 dark:text-zinc-200 focus:outline-none focus:ring-1 focus:ring-violet-500" />
                    </div>

                    <button type="submit" class="w-full px-3 py-2 text-xs font-bold text-zinc-700 dark:text-zinc-300 bg-zinc-50 hover:bg-zinc-100 dark:bg-zinc-800 dark:hover:bg-zinc-800 rounded-xl border border-zinc-200 dark:border-zinc-800/80 transition text-center">
                        Search Anime-Pictures
                    </button>
                </form>
            </div>

        </div>

    </div>

</div>