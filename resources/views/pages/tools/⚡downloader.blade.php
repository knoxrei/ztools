<?php

use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Media Downloader')] class extends Component
{
    // Single File Component logic
};
?>

<div class="min-h-screen pb-16 space-y-8" x-data="{
    activeTab: 'instagram',
    url: '',
    loading: false,
    results: [],
    error: null,
    hasSearched: false,
    playingIndex: null,
    async fetchDownload() {
        if (!this.url.trim()) return;
        this.loading = true;
        this.error = null;
        this.results = [];
        this.hasSearched = true;
        this.playingIndex = null;
        try {
            const formData = new FormData();
            formData.append('url', this.url.trim());
            formData.append('platform', this.activeTab);
            const tokenMeta = document.querySelector('meta[name=\'csrf-token\']');
            formData.append('_token', tokenMeta ? tokenMeta.content : '');
            const res = await fetch('/downloader/fetch', {
                method: 'POST',
                body: formData,
            });
            const json = await res.json();
            if (json.success) {
                this.results = json.items;
            } else {
                this.error = json.message || 'Something went wrong.';
            }
        } catch (e) {
            this.error = 'Network error — please try again.';
        } finally {
            this.loading = false;
        }
    },
    reset() {
        this.url = '';
        this.results = [];
        this.error = null;
        this.hasSearched = false;
        this.playingIndex = null;
    }
}">

    <!-- Page Header -->
    <div class="flex items-center gap-3">
        <div class="p-2 text-violet-600 dark:text-violet-400">
            <flux:icon icon="arrow-down-tray" class="size-7" />
        </div>
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white tracking-tight">Media Downloader</h1>
            <p class="text-xs text-zinc-500 dark:text-zinc-400">
                Download Instagram posts, reels, images, or TikTok videos. Choose the platform and fetch your media.
            </p>
        </div>
    </div>

    <!-- Input Card -->
    <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-sm overflow-hidden flex flex-col">
        <!-- Tab Switcher -->
        <div class="flex border-b border-zinc-200 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-900/50">
            <button
                type="button"
                @click="activeTab = 'instagram'; reset();"
                :class="activeTab === 'instagram' ? 'border-violet-600 text-violet-600 dark:text-violet-400 dark:border-violet-400 font-bold bg-white dark:bg-zinc-900' : 'border-transparent text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-300'"
                class="flex-1 py-3.5 text-center border-b-2 text-xs font-bold uppercase tracking-wider transition">
                <span class="flex items-center justify-center gap-2">
                    <flux:icon icon="photo" class="size-4" />
                    Instagram Downloader
                </span>
            </button>
            <button
                type="button"
                @click="activeTab = 'tiktok'; reset();"
                :class="activeTab === 'tiktok' ? 'border-violet-600 text-violet-600 dark:text-violet-400 dark:border-violet-400 font-bold bg-white dark:bg-zinc-900' : 'border-transparent text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-300'"
                class="flex-1 py-3.5 text-center border-b-2 text-xs font-bold uppercase tracking-wider transition">
                <span class="flex items-center justify-center gap-2">
                    <flux:icon icon="play" class="size-4" />
                    TikTok Downloader
                </span>
            </button>
        </div>

        <div class="p-6 space-y-5">
            <!-- Instagram Input tab content -->
            <template x-if="activeTab === 'instagram'">
                <div class="space-y-2">
                    <label class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-widest">
                        Instagram URL (Post, Reel, Carousel, or Image)
                    </label>

                    <div class="relative flex items-center">
                        <div class="absolute left-3.5 top-1/2 -translate-y-1/2 text-zinc-400 dark:text-zinc-500 pointer-events-none">
                            <flux:icon icon="link" class="size-4" />
                        </div>
                        <input
                            type="text"
                            name="url"
                            x-model="url"
                            placeholder="instagram.com/p/ABC123  or  instagram.com/reel/..."
                            class="w-full pl-10 pr-28 py-2.5 rounded-xl bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 text-sm font-mono text-zinc-800 dark:text-zinc-200 focus:outline-none focus:ring-2 focus:ring-violet-500 dark:focus:ring-violet-400 transition placeholder-zinc-400 dark:placeholder-zinc-600" />
                        <button
                            type="button"
                            @click="fetchDownload()"
                            :disabled="loading || !url.trim()"
                            class="absolute right-1.5 top-1.5 bottom-1.5 flex items-center gap-1.5 px-3.5 text-xs font-bold text-white bg-violet-600 hover:bg-violet-700 disabled:opacity-50 disabled:cursor-not-allowed rounded-lg transition shadow-sm">
                            <template x-if="loading">
                                <svg class="animate-spin size-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                                </svg>
                            </template>
                            <template x-if="!loading">
                                <flux:icon icon="arrow-down-tray" class="size-3" />
                            </template>
                            <span x-text="loading ? 'Fetching...' : 'Download'"></span>
                        </button>
                    </div>
                </div>
            </template>

            <!-- TikTok Input tab content -->
            <template x-if="activeTab === 'tiktok'">
                <div class="space-y-2">
                    <label class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-widest">
                        TikTok URL (Video or MP3 Audio)
                    </label>

                    <div class="relative flex items-center">
                        <div class="absolute left-3.5 top-1/2 -translate-y-1/2 text-zinc-400 dark:text-zinc-500 pointer-events-none">
                            <flux:icon icon="link" class="size-4" />
                        </div>
                        <input
                            type="text"
                            name="url"
                            x-model="url"
                            placeholder="tiktok.com/@username/video/...  or  vt.tiktok.com/..."
                            class="w-full pl-10 pr-28 py-2.5 rounded-xl bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 text-sm font-mono text-zinc-800 dark:text-zinc-200 focus:outline-none focus:ring-2 focus:ring-violet-500 dark:focus:ring-violet-400 transition placeholder-zinc-400 dark:placeholder-zinc-600" />
                        <button
                            type="button"
                            @click="fetchDownload()"
                            :disabled="loading || !url.trim()"
                            class="absolute right-1.5 top-1.5 bottom-1.5 flex items-center gap-1.5 px-3.5 text-xs font-bold text-white bg-violet-600 hover:bg-violet-700 disabled:opacity-50 disabled:cursor-not-allowed rounded-lg transition shadow-sm">
                            <template x-if="loading">
                                <svg class="animate-spin size-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                                </svg>
                            </template>
                            <template x-if="!loading">
                                <flux:icon icon="arrow-down-tray" class="size-3" />
                            </template>
                            <span x-text="loading ? 'Fetching...' : 'Download'"></span>
                        </button>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- Error state -->
    <div
        x-show="error !== null"
        x-transition
        class="bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-800/60 rounded-2xl p-5 flex items-start gap-3"
        x-cloak>
        <div class="p-1.5 rounded-lg bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 shrink-0 mt-0.5">
            <flux:icon icon="exclamation-triangle" class="size-4" />
        </div>
        <div>
            <p class="text-xs font-bold text-red-700 dark:text-red-300 mb-0.5">Download Failed</p>
            <p class="text-xs text-red-600 dark:text-red-400" x-text="error"></p>
        </div>
    </div>

    <!-- Empty / no results state (after a search with 0 items) -->
    <div
        x-show="hasSearched && !loading && results.length === 0 && error === null"
        x-transition
        class="bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-10 text-center space-y-3"
        x-cloak>
        <div class="mx-auto w-12 h-12 rounded-xl bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center">
            <flux:icon icon="photo" class="size-5 text-zinc-400" />
        </div>
        <p class="text-sm font-semibold text-zinc-500 dark:text-zinc-400">No media found</p>
        <p class="text-xs text-zinc-400 dark:text-zinc-600">The post may be private, expired, or the URL is unsupported.</p>
    </div>

    <!-- Results Grid -->
    <div
        x-show="results.length > 0"
        x-transition
        class="space-y-4"
        x-cloak>
        <div class="flex items-center justify-between">
            <h2 class="text-xs font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-widest">
                Results &mdash; <span x-text="results.length"></span> file<span x-show="results.length !== 1">s</span>
            </h2>
            <button
                type="button"
                @click="reset()"
                class="text-[10px] font-bold text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 transition">
                Clear
            </button>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            <template x-for="(item, i) in results" :key="i">
                <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-sm overflow-hidden flex flex-col">
                    <!-- Thumbnail preview — hidden if image fails to load -->
                    <template x-if="item.thumb">
                        <div class="relative w-full bg-zinc-100 dark:bg-zinc-950 aspect-video overflow-hidden">
                            <!-- Inline video player when play button is clicked -->
                            <template x-if="playingIndex === i">
                                <div class="w-full h-full bg-black relative">
                                    <video
                                        :src="item.url"
                                        controls
                                        autoplay
                                        class="w-full h-full"
                                        style="object-fit: contain;">
                                    </video>
                                    <button
                                        type="button"
                                        @click="playingIndex = null"
                                        class="absolute top-2 right-2 p-1.5 bg-black/60 hover:bg-black/80 rounded-full text-white transition"
                                        title="Close Preview">
                                        <flux:icon icon="x-mark" class="size-3.5" />
                                    </button>
                                </div>
                            </template>

                            <!-- Static thumbnail & video play overlay -->
                            <template x-if="playingIndex !== i">
                                <div class="relative w-full h-full">
                                    <img
                                        :src="'/downloader/thumb?url=' + encodeURIComponent(item.thumb)"
                                        :alt="'Media preview ' + (i + 1)"
                                        loading="lazy"
                                        onerror="this.style.display='none'; this.parentNode.querySelector('.fallback-preview').style.display='flex'; const badge = this.parentNode.querySelector('.index-badge'); if(badge) badge.style.display='none'; const playBtn = this.parentNode.querySelector('.play-btn'); if(playBtn) playBtn.style.display='none';"
                                        class="w-full h-full object-cover transition duration-300 hover:scale-105" />

                                    <!-- Play overlay for reels/videos -->
                                    <template x-if="item.url.includes('.mp4') || item.filename.includes('.mp4') || (item.label && item.label.toLowerCase().includes('video'))">
                                        <button
                                            type="button"
                                            @click="playingIndex = i"
                                            class="play-btn absolute inset-0 flex items-center justify-center bg-black/20 hover:bg-black/40 transition group"
                                            title="Play Video Preview">
                                            <div class="p-3 bg-white/95 dark:bg-zinc-900/95 rounded-full shadow-lg text-violet-600 dark:text-violet-400 group-hover:scale-110 transition duration-300">
                                                <flux:icon icon="play" class="size-6 fill-current" />
                                            </div>
                                        </button>
                                    </template>

                                    <!-- Fallback icon when image fails -->
                                    <div style="display: none;"
                                        class="fallback-preview absolute inset-0 flex flex-col items-center justify-center gap-2 text-zinc-400 dark:text-zinc-600">
                                        <flux:icon icon="film" class="size-8" />
                                        <span class="text-[10px] font-semibold uppercase tracking-widest">Preview unavailable</span>
                                    </div>
                                    <!-- Index badge -->
                                    <div class="index-badge absolute top-2 left-2 px-2 py-0.5 bg-black/60 backdrop-blur-sm rounded-full text-[10px] font-bold text-white">
                                        #<span x-text="i + 1"></span>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>

                    <div class="p-4 flex flex-col gap-3 flex-1">
                        <div class="flex items-center justify-between gap-2">
                            <p class="text-[10px] font-mono text-zinc-400 dark:text-zinc-600 truncate flex-1" x-text="item.filename"></p>
                            <!-- Badge specifying media type -->
                            <span
                                x-text="item.url.includes('.mp4') || item.filename.includes('.mp4') || (item.label && item.label.toLowerCase().includes('video')) ? 'Video/Reel' : 'Image'"
                                :class="item.url.includes('.mp4') || item.filename.includes('.mp4') || (item.label && item.label.toLowerCase().includes('video')) 
                                    ? 'bg-violet-50 dark:bg-violet-950/40 text-violet-600 dark:text-violet-400 border border-violet-100 dark:border-violet-800/40' 
                                    : 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-800/40'"
                                class="px-1.5 py-0.5 rounded text-[8px] font-bold uppercase tracking-wider shrink-0">
                            </span>
                        </div>
                        <a
                            :href="'/downloader/download?url=' + encodeURIComponent(item.url) + '&filename=' + encodeURIComponent(item.filename)"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="w-full flex items-center justify-center gap-2 px-3 py-2.5 text-xs font-bold text-white bg-violet-600 hover:bg-violet-700 rounded-xl transition shadow-sm">
                            <flux:icon icon="arrow-down-tray" class="size-3.5" />
                            <span>Download</span>
                        </a>
                    </div>
                </div>
            </template>
        </div>

    </div>

    <!-- Loading skeleton -->
    <div x-show="loading" x-transition class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5" x-cloak>
        <template x-for="n in 3" :key="n">
            <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 overflow-hidden animate-pulse">
                <div class="aspect-square bg-zinc-100 dark:bg-zinc-800"></div>
                <div class="p-4 space-y-3">
                    <div class="h-2 bg-zinc-100 dark:bg-zinc-800 rounded-full w-3/4"></div>
                    <div class="h-8 bg-zinc-100 dark:bg-zinc-800 rounded-xl"></div>
                </div>
            </div>
        </template>
    </div>

    <!-- Idle placeholder -->
    <div
        x-show="!hasSearched && !loading"
        x-transition
        class="bg-zinc-50 dark:bg-zinc-900/50 border border-dashed border-zinc-200 dark:border-zinc-800 rounded-2xl p-10 text-center space-y-4"
        x-cloak>
        <div class="mx-auto w-14 h-14 rounded-2xl bg-violet-50 dark:bg-violet-950/30 flex items-center justify-center">
            <flux:icon icon="arrow-down-tray" class="size-6 text-violet-500 dark:text-violet-400" />
        </div>
        <div class="space-y-1">
            <p class="text-sm font-semibold text-zinc-600 dark:text-zinc-400">Paste an Instagram or TikTok URL above</p>
            <p class="text-xs text-zinc-400 dark:text-zinc-600">Supports Instagram posts, reels, carousels &amp; TikTok videos or MP3 audio</p>
        </div>
        <div class="flex flex-wrap justify-center gap-2 pt-2">
            <span class="px-2.5 py-1 text-[10px] font-bold text-zinc-500 dark:text-zinc-500 bg-zinc-100 dark:bg-zinc-800 rounded-full border border-zinc-200 dark:border-zinc-700">Instagram Posts &amp; Reels</span>
            <span class="px-2.5 py-1 text-[10px] font-bold text-zinc-500 dark:text-zinc-500 bg-zinc-100 dark:bg-zinc-800 rounded-full border border-zinc-200 dark:border-zinc-700">Instagram Carousel Images</span>
            <span class="px-2.5 py-1 text-[10px] font-bold text-zinc-500 dark:text-zinc-500 bg-zinc-100 dark:bg-zinc-800 rounded-full border border-zinc-200 dark:border-zinc-700">TikTok Videos (No Watermark)</span>
            <span class="px-2.5 py-1 text-[10px] font-bold text-zinc-500 dark:text-zinc-500 bg-zinc-100 dark:bg-zinc-800 rounded-full border border-zinc-200 dark:border-zinc-700">TikTok MP3 Audio</span>
        </div>
    </div>

</div>