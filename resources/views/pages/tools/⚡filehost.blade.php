<?php

use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Anonymous File Upload')] class extends Component
{
    public string $activeTab = 'filehost';
    public string $activeSection = 'standard'; // standard | experimental | short | advanced

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->activeSection = 'standard';
    }

    public function setSection(string $section): void
    {
        $this->activeSection = $section;
    }
};
?>

<div class="min-h-screen pb-16" x-data="{
    activeTab: @entangle('activeTab'),
    clearFile(formEl) {
        formEl.reset();
        formEl.querySelectorAll('.file-name-label').forEach(el => el.textContent = 'No file chosen');
        formEl.querySelectorAll('.file-drop-zone').forEach(el => el.classList.remove('has-file'));
    },
    setupDropzone(inputId) {
        const input = document.getElementById(inputId);
        if (!input) return;
        const zone = input.closest('.file-drop-zone');
        if (!zone) return;
        ['dragenter','dragover'].forEach(e => zone.addEventListener(e, ev => { ev.preventDefault(); zone.classList.add('dragging'); }));
        ['dragleave','drop'].forEach(e => zone.addEventListener(e, ev => { ev.preventDefault(); zone.classList.remove('dragging'); }));
        zone.addEventListener('drop', ev => {
            ev.preventDefault();
            const files = ev.dataTransfer.files;
            if (files.length) {
                const dt = new DataTransfer();
                Array.from(files).forEach(f => dt.items.add(f));
                input.files = dt.files;
                input.dispatchEvent(new Event('change'));
            }
        });
        input.addEventListener('change', () => {
            const label = zone.querySelector('.file-name-label');
            if (input.files.length > 0) {
                const names = Array.from(input.files).map(f => f.name).join(', ');
                const truncated = names.length > 50 ? names.substring(0, 47) + '...' : names;
                if (label) label.textContent = truncated;
                zone.classList.add('has-file');
            }
        });
    }
}">

    {{-- Page Header --}}
    <div class="mb-8">
        <div class="flex items-center gap-3 mb-2">
            <div class="p-2">
                <flux:icon icon="arrow-up-tray" class="size-7 text-violet-600 dark:text-violet-400" />
            </div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white tracking-tight">Anonymous File Upload</h1>
        </div>
        <p class="text-sm text-zinc-500 dark:text-zinc-400 ml-14">
            Upload files & images anonymously to external hosting services. No account required.
        </p>
    </div>

    {{-- Notice --}}
    <div class="mb-6 p-3 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-start gap-3">
        <flux:icon icon="exclamation-triangle" class="size-4 text-amber-500 shrink-0 mt-0.5" />
        <p class="text-xs text-amber-700 dark:text-amber-400 leading-relaxed">
            Files are uploaded directly to third-party services. Z-Knox does not store, track, or process your files. Always review each service's privacy policy before uploading sensitive content.
        </p>
    </div>

    {{-- Tab Navigation --}}
    <div class="flex gap-1 p-1 bg-zinc-100 dark:bg-zinc-800/60 rounded-xl w-fit mb-8 border border-zinc-200 dark:border-zinc-700">
        <button wire:click="setTab('filehost')"
            class="px-5 py-2 rounded-lg text-xs font-bold uppercase tracking-wider transition {{ $activeTab === 'filehost' ? 'bg-white dark:bg-zinc-900 text-violet-600 dark:text-violet-400 shadow-sm' : 'text-zinc-500 dark:text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200' }}">
            <span class="flex items-center gap-2">
                <flux:icon icon="archive-box" class="size-3.5" />
                File Hosts
            </span>
        </button>
        <button wire:click="setTab('imagehost')"
            class="px-5 py-2 rounded-lg text-xs font-bold uppercase tracking-wider transition {{ $activeTab === 'imagehost' ? 'bg-white dark:bg-zinc-900 text-violet-600 dark:text-violet-400 shadow-sm' : 'text-zinc-500 dark:text-zinc-400 hover:text-zinc-700 dark:hover:text-zinc-200' }}">
            <span class="flex items-center gap-2">
                <flux:icon icon="photo" class="size-3.5" />
                Image Hosts
            </span>
        </button>
    </div>

    {{-- ====================== FILE HOST TAB ====================== --}}
    @if($activeTab === 'filehost')
    <div class="space-y-4">

        {{-- Section Switcher --}}
        <div class="flex flex-wrap gap-2 mb-6">
            @foreach([
                ['standard', '📦', 'Standard Hosts'],
                ['experimental', '🧪', 'Experimental'],
                ['short', '♻', 'Short Retention'],
                ['advanced', '⚠', 'Advanced Users'],
            ] as [$key, $icon, $label])
            <button wire:click="setSection('{{ $key }}')"
                class="flex items-center gap-1.5 px-4 py-1.5 rounded-full text-xs font-semibold border transition
                    {{ $activeSection === $key
                        ? 'bg-violet-600 border-violet-600 text-white'
                        : 'border-zinc-300 dark:border-zinc-700 text-zinc-500 dark:text-zinc-400 hover:border-violet-400 hover:text-violet-500' }}">
                <span>{{ $icon }}</span> {{ $label }}
            </button>
            @endforeach
        </div>

        {{-- STANDARD FILE HOSTS --}}
        @if($activeSection === 'standard')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

            {{-- Gofile --}}
            <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800/80 shadow-sm p-5 space-y-4" x-init="setupDropzone('gofile-input')">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-zinc-800 dark:text-zinc-100">Gofile</h3>
                        <p class="text-xs text-zinc-400 mt-0.5">Unlimited size · Files removed after 10 days without downloads</p>
                    </div>
                    <span class="px-2 py-0.5 bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400 text-[10px] font-bold rounded-full uppercase">Recommended</span>
                </div>
                <form action="https://upload.gofile.io/contents/uploadFile" method="POST" enctype="multipart/form-data" rel="noreferrer" target="_blank">
                    <label class="file-drop-zone flex flex-col items-center justify-center border-2 border-dashed border-zinc-300 dark:border-zinc-700 rounded-xl p-5 cursor-pointer hover:border-violet-500 hover:bg-violet-50/30 dark:hover:bg-violet-900/10 transition has-file:border-emerald-500 has-file:bg-emerald-50/30 dragging:border-violet-600 dragging:bg-violet-50/40 mb-3">
                        <flux:icon icon="arrow-up-tray" class="size-7 text-zinc-400 mb-2" />
                        <span class="file-name-label text-xs text-zinc-500 dark:text-zinc-400 text-center">Drop file here or click to browse</span>
                        <input id="gofile-input" type="file" name="file" required class="hidden">
                    </label>
                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 py-2 px-4 bg-violet-600 hover:bg-violet-700 text-white text-xs font-bold rounded-xl transition flex items-center justify-center gap-2">
                            <flux:icon icon="arrow-up-tray" class="size-3.5" />
                            Upload to Gofile
                        </button>
                        <button type="reset" onclick="clearFile(this.closest('form'))" class="px-3 py-2 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-500 text-xs font-bold rounded-xl transition">✕</button>
                    </div>
                </form>
            </div>

            {{-- FileMirage --}}
            <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800/80 shadow-sm p-5 space-y-4" x-init="setupDropzone('filemirage-input')">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-zinc-800 dark:text-zinc-100">FileMirage</h3>
                        <p class="text-xs text-zinc-400 mt-0.5">Up to 50 GB · Files removed after 15 days without downloads</p>
                    </div>
                    <span class="px-2 py-0.5 bg-blue-500/10 border border-blue-500/20 text-blue-600 dark:text-blue-400 text-[10px] font-bold rounded-full uppercase">50 GB</span>
                </div>
                <form action="https://store1.filemirage.com/upload.php" method="POST" enctype="multipart/form-data" rel="noreferrer" target="_blank">
                    <label class="file-drop-zone flex flex-col items-center justify-center border-2 border-dashed border-zinc-300 dark:border-zinc-700 rounded-xl p-5 cursor-pointer hover:border-violet-500 hover:bg-violet-50/30 dark:hover:bg-violet-900/10 transition has-file:border-emerald-500 dragging:border-violet-600 mb-3">
                        <flux:icon icon="arrow-up-tray" class="size-7 text-zinc-400 mb-2" />
                        <span class="file-name-label text-xs text-zinc-500 dark:text-zinc-400 text-center">Drop file here or click to browse</span>
                        <input id="filemirage-input" type="file" name="file" required class="hidden">
                    </label>
                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 py-2 px-4 bg-violet-600 hover:bg-violet-700 text-white text-xs font-bold rounded-xl transition flex items-center justify-center gap-2">
                            <flux:icon icon="arrow-up-tray" class="size-3.5" />
                            Upload to FileMirage
                        </button>
                        <button type="reset" class="px-3 py-2 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-500 text-xs font-bold rounded-xl transition">✕</button>
                    </div>
                </form>
            </div>

            {{-- Anonfile --}}
            <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800/80 shadow-sm p-5 space-y-4" x-init="setupDropzone('anonfile-input')">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-zinc-800 dark:text-zinc-100">Anonfile</h3>
                        <p class="text-xs text-zinc-400 mt-0.5">Up to 512 MB · Multiple mirrors available · 90 days retention</p>
                    </div>
                    <span class="px-2 py-0.5 bg-zinc-500/10 border border-zinc-500/20 text-zinc-500 dark:text-zinc-400 text-[10px] font-bold rounded-full uppercase">512 MB</span>
                </div>
                <form method="POST" enctype="multipart/form-data" rel="noreferrer" target="_blank">
                    <input type="hidden" name="sess_id">
                    <input type="hidden" name="utype" value="anon">
                    <input type="hidden" name="link_rcpt">
                    <input type="hidden" name="link_pass">
                    <input type="hidden" name="to_folder">
                    <input type="hidden" name="file_descr">
                    <input type="hidden" name="file_public">
                    <label class="file-drop-zone flex flex-col items-center justify-center border-2 border-dashed border-zinc-300 dark:border-zinc-700 rounded-xl p-5 cursor-pointer hover:border-violet-500 hover:bg-violet-50/30 dark:hover:bg-violet-900/10 transition has-file:border-emerald-500 dragging:border-violet-600 mb-3">
                        <flux:icon icon="arrow-up-tray" class="size-7 text-zinc-400 mb-2" />
                        <span class="file-name-label text-xs text-zinc-500 dark:text-zinc-400 text-center">Drop files here (up to 3 files)</span>
                        <input id="anonfile-input" type="file" name="file_0" multiple required class="hidden">
                    </label>
                    <div class="flex gap-2">
                        <button type="submit" formaction="https://file-09.anonfile.de/cgi-bin/upload.cgi?upload_type=file&utype=anon" class="flex-1 py-2 px-4 bg-violet-600 hover:bg-violet-700 text-white text-xs font-bold rounded-xl transition flex items-center justify-center gap-2">
                            <flux:icon icon="arrow-up-tray" class="size-3.5" />
                            Mirror 1
                        </button>
                        <button type="submit" formaction="https://file-07.anonfile.de/cgi-bin/upload.cgi?upload_type=file&utype=anon" class="py-2 px-3 bg-zinc-700 hover:bg-zinc-600 text-white text-xs font-bold rounded-xl transition">M2</button>
                        <button type="submit" formaction="https://file-06.anonfile.de/cgi-bin/upload.cgi?upload_type=file&utype=anon" class="py-2 px-3 bg-zinc-700 hover:bg-zinc-600 text-white text-xs font-bold rounded-xl transition">M3</button>
                        <button type="reset" class="px-3 py-2 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-500 text-xs font-bold rounded-xl transition">✕</button>
                    </div>
                    <p class="text-[10px] text-zinc-400 mt-2">Files accessible at <code class="font-mono">https://anonfile.de/&lt;code&gt;</code></p>
                </form>
            </div>

            {{-- UploadFlix --}}
            <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800/80 shadow-sm p-5 space-y-4" x-init="setupDropzone('uploadflix-input')">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-zinc-800 dark:text-zinc-100">UploadFlix</h3>
                        <p class="text-xs text-zinc-400 mt-0.5">Up to 3 GB · Up to 10 files at once · Multiple mirrors</p>
                    </div>
                    <span class="px-2 py-0.5 bg-blue-500/10 border border-blue-500/20 text-blue-600 dark:text-blue-400 text-[10px] font-bold rounded-full uppercase">3 GB</span>
                </div>
                <form method="POST" enctype="multipart/form-data" rel="noreferrer" target="_blank">
                    <input type="hidden" name="sess_id">
                    <input type="hidden" name="utype" value="anon">
                    <input type="hidden" name="file_descr">
                    <input type="hidden" name="file_public" value="1">
                    <input type="hidden" name="link_rcpt">
                    <input type="hidden" name="link_pass">
                    <input type="hidden" name="to_folder">
                    <input type="hidden" name="upload" value="Start upload">
                    <input type="hidden" name="keepalive" value="1">
                    <label class="file-drop-zone flex flex-col items-center justify-center border-2 border-dashed border-zinc-300 dark:border-zinc-700 rounded-xl p-5 cursor-pointer hover:border-violet-500 hover:bg-violet-50/30 dark:hover:bg-violet-900/10 transition has-file:border-emerald-500 dragging:border-violet-600 mb-3">
                        <flux:icon icon="arrow-up-tray" class="size-7 text-zinc-400 mb-2" />
                        <span class="file-name-label text-xs text-zinc-500 dark:text-zinc-400 text-center">Drop files here (up to 10 files)</span>
                        <input id="uploadflix-input" type="file" name="file_0" multiple required class="hidden">
                    </label>
                    <div class="flex gap-2">
                        <button type="submit" formaction="https://web.secure-storage.top/cgi-bin/upload.cgi?upload_type=file&utype=anon" class="flex-1 py-2 px-4 bg-violet-600 hover:bg-violet-700 text-white text-xs font-bold rounded-xl transition flex items-center justify-center gap-2">
                            <flux:icon icon="arrow-up-tray" class="size-3.5" />
                            Mirror 1
                        </button>
                        <button type="submit" formaction="https://cloud.secure-storage.top/cgi-bin/upload.cgi?upload_type=file&utype=anon" class="py-2 px-3 bg-zinc-700 hover:bg-zinc-600 text-white text-xs font-bold rounded-xl transition">M2</button>
                        <button type="reset" class="px-3 py-2 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-500 text-xs font-bold rounded-xl transition">✕</button>
                    </div>
                </form>
            </div>

            {{-- 1fichier + other links --}}
            <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800/80 shadow-sm p-5 space-y-3 md:col-span-2">
                <h3 class="text-xs font-bold text-zinc-500 dark:text-zinc-400 uppercase tracking-widest">Other File Hosts (External Links)</h3>
                <div class="flex flex-wrap gap-3">
                    <a href="https://1fichier.com/" rel="noreferrer" target="_blank"
                        class="flex items-center gap-2 px-4 py-2 bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl text-xs font-semibold text-zinc-700 dark:text-zinc-300 hover:border-violet-400 hover:text-violet-600 dark:hover:text-violet-400 transition">
                        <flux:icon icon="arrow-top-right-on-square" class="size-3.5" />
                        1fichier <cite class="text-zinc-400 font-normal ml-1">[300 GB]</cite>
                    </a>
                    <a href="http://uploda5fsvttb7z7m5rrkh6geqpwgrhqknhbz5umervr247scx5xruyd.onion/" target="_blank"
                        class="flex items-center gap-2 px-4 py-2 bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl text-xs font-semibold text-zinc-700 dark:text-zinc-300 hover:border-violet-400 hover:text-violet-600 dark:hover:text-violet-400 transition">
                        <flux:icon icon="globe-alt" class="size-3.5" />
                        Uploada (.onion) <cite class="text-zinc-400 font-normal ml-1">[&gt;5 GB]</cite>
                    </a>
                    <a href="https://upload.ee/" rel="noreferrer" target="_blank"
                        class="flex items-center gap-2 px-4 py-2 bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl text-xs font-semibold text-zinc-700 dark:text-zinc-300 hover:border-violet-400 hover:text-violet-600 dark:hover:text-violet-400 transition">
                        <flux:icon icon="arrow-top-right-on-square" class="size-3.5" />
                        upload.ee <cite class="text-zinc-400 font-normal ml-1">[100 MB]</cite>
                    </a>
                </div>
            </div>

        </div>
        @endif

        {{-- EXPERIMENTAL FILE HOSTS --}}
        @if($activeSection === 'experimental')
        <div class="mb-4 p-3 rounded-xl bg-blue-500/10 border border-blue-500/20 flex items-start gap-2">
            <flux:icon icon="information-circle" class="size-4 text-blue-500 shrink-0 mt-0.5" />
            <p class="text-xs text-blue-700 dark:text-blue-400">These hosts may be unstable or have limited availability. Use with caution.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

            {{-- Shadow Files (onion) --}}
            <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800/80 shadow-sm p-5 space-y-4" x-init="setupDropzone('shadow-input')">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-zinc-800 dark:text-zinc-100">Shadow Files</h3>
                        <p class="text-xs text-zinc-400 mt-0.5">Onion · Up to 100 MB</p>
                    </div>
                    <span class="px-2 py-0.5 bg-zinc-800 border border-zinc-700 text-zinc-400 text-[10px] font-bold rounded-full uppercase">Tor Only</span>
                </div>
                <form action="http://shadowedpyfzyrniv63p22szghpkbhvbvpkipjd2wkb44icq2fd6cxid.onion/" method="POST" enctype="multipart/form-data" rel="noreferrer" target="_blank">
                    <label class="file-drop-zone flex flex-col items-center justify-center border-2 border-dashed border-zinc-300 dark:border-zinc-700 rounded-xl p-5 cursor-pointer hover:border-violet-500 hover:bg-violet-50/30 dark:hover:bg-violet-900/10 transition has-file:border-emerald-500 dragging:border-violet-600 mb-3">
                        <flux:icon icon="arrow-up-tray" class="size-7 text-zinc-400 mb-2" />
                        <span class="file-name-label text-xs text-zinc-500 dark:text-zinc-400 text-center">Requires Tor Browser</span>
                        <input id="shadow-input" type="file" name="file" required class="hidden">
                    </label>
                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 py-2 px-4 bg-zinc-800 hover:bg-zinc-700 text-white text-xs font-bold rounded-xl transition flex items-center justify-center gap-2">
                            <flux:icon icon="arrow-up-tray" class="size-3.5" />
                            Upload (Tor)
                        </button>
                        <button type="reset" class="px-3 py-2 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-500 text-xs font-bold rounded-xl transition">✕</button>
                    </div>
                </form>
            </div>

            {{-- Eternal (onion) --}}
            <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800/80 shadow-sm p-5 space-y-4" x-init="setupDropzone('eternal-input')">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-zinc-800 dark:text-zinc-100">Eternal</h3>
                        <p class="text-xs text-zinc-400 mt-0.5">Onion · Up to 500 MB · Configurable expiry</p>
                    </div>
                    <span class="px-2 py-0.5 bg-zinc-800 border border-zinc-700 text-zinc-400 text-[10px] font-bold rounded-full uppercase">Tor Only</span>
                </div>
                <form action="http://eternalcbrzpicytj4zyguygpmkjlkddxob7tptlr25cdipe5svyqoqd.onion/host/" method="POST" enctype="multipart/form-data" rel="noreferrer" target="_blank">
                    <input type="hidden" name="upload" value="true">
                    <input type="hidden" name="text">
                    <label class="file-drop-zone flex flex-col items-center justify-center border-2 border-dashed border-zinc-300 dark:border-zinc-700 rounded-xl p-5 cursor-pointer hover:border-violet-500 hover:bg-violet-50/30 dark:hover:bg-violet-900/10 transition has-file:border-emerald-500 dragging:border-violet-600 mb-3">
                        <flux:icon icon="arrow-up-tray" class="size-7 text-zinc-400 mb-2" />
                        <span class="file-name-label text-xs text-zinc-500 dark:text-zinc-400 text-center">Requires Tor Browser</span>
                        <input id="eternal-input" type="file" name="file" required class="hidden">
                    </label>
                    <div class="flex flex-col gap-2">
                        <select name="expires" class="w-full px-3 py-2 rounded-xl border border-zinc-300 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 text-xs text-zinc-700 dark:text-zinc-300">
                            <option value="86400">Expires: 1 day</option>
                            <option value="604800">Expires: 1 week</option>
                            <option value="2592000">Expires: 1 month</option>
                            <option value="31536000" selected>Expires: 1 year</option>
                            <option value="0">Never expires</option>
                        </select>
                        <div class="flex gap-2">
                            <button type="submit" class="flex-1 py-2 px-4 bg-zinc-800 hover:bg-zinc-700 text-white text-xs font-bold rounded-xl transition flex items-center justify-center gap-2">
                                <flux:icon icon="arrow-up-tray" class="size-3.5" />
                                Upload (Tor)
                            </button>
                            <button type="reset" class="px-3 py-2 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-500 text-xs font-bold rounded-xl transition">✕</button>
                        </div>
                        <p class="text-[10px] text-zinc-400">Replace <code class="font-mono">/file/</code> with <code class="font-mono">/download/</code> in link for direct download.</p>
                    </div>
                </form>
            </div>

            {{-- Nelion --}}
            <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800/80 shadow-sm p-5 space-y-4" x-init="setupDropzone('nelion-input')">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-zinc-800 dark:text-zinc-100">Nelion</h3>
                        <p class="text-xs text-zinc-400 mt-0.5">Up to 512 MB · 7 days retention</p>
                    </div>
                    <span class="px-2 py-0.5 bg-zinc-500/10 border border-zinc-500/20 text-zinc-500 dark:text-zinc-400 text-[10px] font-bold rounded-full uppercase">512 MB</span>
                </div>
                <form action="https://eh200.nelion.me/cgi-bin/upload.cgi?upload_type=file&utype=anon" method="POST" enctype="multipart/form-data" rel="noreferrer" target="_blank">
                    <input type="hidden" name="sess_id">
                    <input type="hidden" name="utype" value="anon">
                    <input type="hidden" name="file_descr">
                    <input type="hidden" name="file_public" value="1">
                    <input type="hidden" name="link_rcpt">
                    <input type="hidden" name="link_pass">
                    <input type="hidden" name="to_folder">
                    <input type="hidden" name="upload" value="Start upload">
                    <input type="hidden" name="keepalive" value="1">
                    <label class="file-drop-zone flex flex-col items-center justify-center border-2 border-dashed border-zinc-300 dark:border-zinc-700 rounded-xl p-5 cursor-pointer hover:border-violet-500 hover:bg-violet-50/30 dark:hover:bg-violet-900/10 transition has-file:border-emerald-500 dragging:border-violet-600 mb-3">
                        <flux:icon icon="arrow-up-tray" class="size-7 text-zinc-400 mb-2" />
                        <span class="file-name-label text-xs text-zinc-500 dark:text-zinc-400 text-center">Drop file here or click to browse</span>
                        <input id="nelion-input" type="file" name="file_0" required class="hidden">
                    </label>
                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 py-2 px-4 bg-violet-600 hover:bg-violet-700 text-white text-xs font-bold rounded-xl transition flex items-center justify-center gap-2">
                            <flux:icon icon="arrow-up-tray" class="size-3.5" />
                            Upload to Nelion
                        </button>
                        <button type="reset" class="px-3 py-2 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-500 text-xs font-bold rounded-xl transition">✕</button>
                    </div>
                    <p class="text-[10px] text-zinc-400 mt-2">Files accessible at <code class="font-mono">https://nelion.me/&lt;code&gt;</code></p>
                </form>
            </div>

            {{-- Upload-1Go (onion) --}}
            <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800/80 shadow-sm p-5 space-y-4" x-init="setupDropzone('upload1go-input')">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-zinc-800 dark:text-zinc-100">Upload-1Go</h3>
                        <p class="text-xs text-zinc-400 mt-0.5">Onion · Up to 1 GB · Unstable</p>
                    </div>
                    <span class="px-2 py-0.5 bg-amber-500/10 border border-amber-500/20 text-amber-600 dark:text-amber-400 text-[10px] font-bold rounded-full uppercase">Unstable</span>
                </div>
                <form action="http://filexvjwolor7evlxrubylnz46scbgn33jdf7na3y65mjyyck2iuubad.onion/" method="POST" enctype="multipart/form-data" rel="noreferrer" target="_blank">
                    <input type="hidden" name="MAX_FILE_SIZE" value="1073741824">
                    <input type="hidden" name="submit" value="Start the upload">
                    <label class="file-drop-zone flex flex-col items-center justify-center border-2 border-dashed border-zinc-300 dark:border-zinc-700 rounded-xl p-5 cursor-pointer hover:border-violet-500 hover:bg-violet-50/30 dark:hover:bg-violet-900/10 transition has-file:border-emerald-500 dragging:border-violet-600 mb-3">
                        <flux:icon icon="arrow-up-tray" class="size-7 text-zinc-400 mb-2" />
                        <span class="file-name-label text-xs text-zinc-500 dark:text-zinc-400 text-center">Requires Tor Browser</span>
                        <input id="upload1go-input" type="file" name="fileToUpload" required class="hidden">
                    </label>
                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 py-2 px-4 bg-zinc-800 hover:bg-zinc-700 text-white text-xs font-bold rounded-xl transition flex items-center justify-center gap-2">
                            <flux:icon icon="arrow-up-tray" class="size-3.5" />
                            Upload (Tor)
                        </button>
                        <button type="reset" class="px-3 py-2 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-500 text-xs font-bold rounded-xl transition">✕</button>
                    </div>
                </form>
            </div>

        </div>
        @endif

        {{-- SHORT RETENTION FILE HOSTS --}}
        @if($activeSection === 'short')
        <div class="mb-4 p-3 rounded-xl bg-red-500/10 border border-red-500/20 flex items-start gap-2">
            <flux:icon icon="exclamation-triangle" class="size-4 text-red-500 shrink-0 mt-0.5" />
            <p class="text-xs text-red-700 dark:text-red-400">These hosts have short file retention periods. Files will be automatically deleted after the specified duration.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

            {{-- AnonTransfer --}}
            <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800/80 shadow-sm p-5 space-y-4" x-init="setupDropzone('anontransfer-input')">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-zinc-800 dark:text-zinc-100">AnonTransfer</h3>
                        <p class="text-xs text-zinc-400 mt-0.5">Up to 10 GB · 1 month retention</p>
                    </div>
                    <span class="px-2 py-0.5 bg-red-500/10 border border-red-500/20 text-red-500 text-[10px] font-bold rounded-full uppercase">1 Month</span>
                </div>
                <form action="https://anontransfer.com/anonymous_upload_handler.php" method="POST" enctype="multipart/form-data" rel="noreferrer" target="_blank">
                    <label class="file-drop-zone flex flex-col items-center justify-center border-2 border-dashed border-zinc-300 dark:border-zinc-700 rounded-xl p-5 cursor-pointer hover:border-violet-500 hover:bg-violet-50/30 dark:hover:bg-violet-900/10 transition has-file:border-emerald-500 dragging:border-violet-600 mb-3">
                        <flux:icon icon="arrow-up-tray" class="size-7 text-zinc-400 mb-2" />
                        <span class="file-name-label text-xs text-zinc-500 dark:text-zinc-400 text-center">Drop file here or click to browse</span>
                        <input id="anontransfer-input" type="file" name="file" required class="hidden">
                    </label>
                    <div class="flex flex-col gap-2">
                        <a href="https://anontransfer.com/" rel="noreferrer" target="_blank"
                            class="text-center py-1.5 text-xs text-violet-600 dark:text-violet-400 hover:underline">
                            Step 1: Visit site first to get session token →
                        </a>
                        <div class="flex gap-2">
                            <button type="submit" class="flex-1 py-2 px-4 bg-violet-600 hover:bg-violet-700 text-white text-xs font-bold rounded-xl transition flex items-center justify-center gap-2">
                                <flux:icon icon="arrow-up-tray" class="size-3.5" />
                                Upload
                            </button>
                            <button type="reset" class="px-3 py-2 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-500 text-xs font-bold rounded-xl transition">✕</button>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Host4Geeks --}}
            <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800/80 shadow-sm p-5 space-y-4" x-init="setupDropzone('h4g-input')">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-zinc-800 dark:text-zinc-100">Host4Geeks</h3>
                        <p class="text-xs text-zinc-400 mt-0.5">Up to 100 MB · 1 day retention</p>
                    </div>
                    <span class="px-2 py-0.5 bg-red-500/10 border border-red-500/20 text-red-500 text-[10px] font-bold rounded-full uppercase">1 Day</span>
                </div>
                <form action="https://files.h4g.co/api.php?d=upload" method="POST" enctype="multipart/form-data" rel="noreferrer" target="_blank">
                    <input type="hidden" name="MAX_FILE_SIZE" value="150000000">
                    <input type="checkbox" name="randomname" checked hidden>
                    <input type="hidden" name="name">
                    <label class="file-drop-zone flex flex-col items-center justify-center border-2 border-dashed border-zinc-300 dark:border-zinc-700 rounded-xl p-5 cursor-pointer hover:border-violet-500 hover:bg-violet-50/30 dark:hover:bg-violet-900/10 transition has-file:border-emerald-500 dragging:border-violet-600 mb-3">
                        <flux:icon icon="arrow-up-tray" class="size-7 text-zinc-400 mb-2" />
                        <span class="file-name-label text-xs text-zinc-500 dark:text-zinc-400 text-center">Drop file here (max 100 MB)</span>
                        <input id="h4g-input" type="file" name="file" required class="hidden">
                    </label>
                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 py-2 px-4 bg-violet-600 hover:bg-violet-700 text-white text-xs font-bold rounded-xl transition flex items-center justify-center gap-2">
                            <flux:icon icon="arrow-up-tray" class="size-3.5" />
                            Upload to Host4Geeks
                        </button>
                        <button type="reset" class="px-3 py-2 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-500 text-xs font-bold rounded-xl transition">✕</button>
                    </div>
                </form>
            </div>

        </div>
        @endif

        {{-- ADVANCED FILE HOSTS --}}
        @if($activeSection === 'advanced')
        <div class="mb-4 p-3 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-start gap-2">
            <flux:icon icon="exclamation-triangle" class="size-4 text-amber-500 shrink-0 mt-0.5" />
            <p class="text-xs text-amber-700 dark:text-amber-400">These hosts require manual steps or knowledge of the underlying upload mechanism. Follow the instructions carefully.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

            {{-- KrakenFiles --}}
            <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800/80 shadow-sm p-5 space-y-4" x-init="setupDropzone('kraken-input')">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-zinc-800 dark:text-zinc-100">KrakenFiles</h3>
                        <p class="text-xs text-zinc-400 mt-0.5">Up to 1 GB · 30 days retention · Multiple servers</p>
                    </div>
                    <span class="px-2 py-0.5 bg-zinc-500/10 border border-zinc-500/20 text-zinc-500 dark:text-zinc-400 text-[10px] font-bold rounded-full uppercase">1 GB</span>
                </div>
                <form method="POST" enctype="multipart/form-data" rel="noreferrer" target="_blank">
                    <label class="file-drop-zone flex flex-col items-center justify-center border-2 border-dashed border-zinc-300 dark:border-zinc-700 rounded-xl p-5 cursor-pointer hover:border-violet-500 hover:bg-violet-50/30 dark:hover:bg-violet-900/10 transition has-file:border-emerald-500 dragging:border-violet-600 mb-3">
                        <flux:icon icon="arrow-up-tray" class="size-7 text-zinc-400 mb-2" />
                        <span class="file-name-label text-xs text-zinc-500 dark:text-zinc-400 text-center">Drop file here or click to browse</span>
                        <input id="kraken-input" type="file" name="files[]" required class="hidden">
                    </label>
                    <div class="flex gap-2">
                        <button type="submit" formaction="https://hs4.krakencloud.net/_uploader/gallery/upload" class="flex-1 py-2 px-4 bg-violet-600 hover:bg-violet-700 text-white text-xs font-bold rounded-xl transition flex items-center justify-center gap-2">
                            <flux:icon icon="arrow-up-tray" class="size-3.5" />
                            Server 1
                        </button>
                        <button type="submit" formaction="https://hs3.krakencloud.net/_uploader/gallery/upload" class="py-2 px-3 bg-zinc-700 hover:bg-zinc-600 text-white text-xs font-bold rounded-xl transition">S2</button>
                        <button type="submit" formaction="https://hs5.krakencloud.net/_uploader/gallery/upload" class="py-2 px-3 bg-zinc-700 hover:bg-zinc-600 text-white text-xs font-bold rounded-xl transition">S3</button>
                        <button type="reset" class="px-3 py-2 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-500 text-xs font-bold rounded-xl transition">✕</button>
                    </div>
                </form>
            </div>

            {{-- Usersdrive --}}
            <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800/80 shadow-sm p-5 space-y-4" x-init="setupDropzone('usersdrive-input')">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-zinc-800 dark:text-zinc-100">Usersdrive</h3>
                        <p class="text-xs text-zinc-400 mt-0.5">Up to 5 GB · Up to 10 files · 16 days retention</p>
                    </div>
                    <span class="px-2 py-0.5 bg-blue-500/10 border border-blue-500/20 text-blue-600 dark:text-blue-400 text-[10px] font-bold rounded-full uppercase">5 GB</span>
                </div>
                <form method="POST" enctype="multipart/form-data" rel="noreferrer" target="_blank">
                    <input type="hidden" name="sess_id">
                    <input type="hidden" name="utype" value="anon">
                    <input type="hidden" name="link_rcpt">
                    <input type="hidden" name="link_pass">
                    <input type="hidden" name="to_folder">
                    <input type="hidden" name="keepalive" value="1">
                    <label class="file-drop-zone flex flex-col items-center justify-center border-2 border-dashed border-zinc-300 dark:border-zinc-700 rounded-xl p-5 cursor-pointer hover:border-violet-500 hover:bg-violet-50/30 dark:hover:bg-violet-900/10 transition has-file:border-emerald-500 dragging:border-violet-600 mb-3">
                        <flux:icon icon="arrow-up-tray" class="size-7 text-zinc-400 mb-2" />
                        <span class="file-name-label text-xs text-zinc-500 dark:text-zinc-400 text-center">Drop files here (up to 10 files)</span>
                        <input id="usersdrive-input" type="file" name="file_0" multiple required class="hidden">
                    </label>
                    <div class="flex gap-2">
                        <button type="submit" formaction="https://dns600.userdrive.org/cgi-bin/upload.cgi?upload_type=file&utype=anon" class="flex-1 py-2 px-4 bg-violet-600 hover:bg-violet-700 text-white text-xs font-bold rounded-xl transition flex items-center justify-center gap-2">
                            <flux:icon icon="arrow-up-tray" class="size-3.5" />
                            Mirror 1
                        </button>
                        <button type="submit" formaction="https://d1000.userdrive.org/cgi-bin/upload.cgi?upload_type=file&utype=anon" class="py-2 px-3 bg-zinc-700 hover:bg-zinc-600 text-white text-xs font-bold rounded-xl transition">M2</button>
                        <button type="submit" formaction="https://dns700.userdrive.org/cgi-bin/upload.cgi?upload_type=file&utype=anon" class="py-2 px-3 bg-zinc-700 hover:bg-zinc-600 text-white text-xs font-bold rounded-xl transition">M3</button>
                        <button type="reset" class="px-3 py-2 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-500 text-xs font-bold rounded-xl transition">✕</button>
                    </div>
                    <p class="text-[10px] text-zinc-400 mt-2">Files accessible at <code class="font-mono">https://usersdrive.com/&lt;code&gt;</code></p>
                </form>
            </div>

        </div>
        @endif

    </div>
    @endif

    {{-- ====================== IMAGE HOST TAB ====================== --}}
    @if($activeTab === 'imagehost')
    <div class="space-y-4">

        {{-- Section Switcher --}}
        <div class="flex flex-wrap gap-2 mb-6">
            @foreach([
                ['standard', '🖼', 'Standard Hosts'],
                ['short', '♻', 'Short Retention'],
            ] as [$key, $icon, $label])
            <button wire:click="setSection('{{ $key }}')"
                class="flex items-center gap-1.5 px-4 py-1.5 rounded-full text-xs font-semibold border transition
                    {{ $activeSection === $key
                        ? 'bg-violet-600 border-violet-600 text-white'
                        : 'border-zinc-300 dark:border-zinc-700 text-zinc-500 dark:text-zinc-400 hover:border-violet-400 hover:text-violet-500' }}">
                <span>{{ $icon }}</span> {{ $label }}
            </button>
            @endforeach
        </div>

        {{-- STANDARD IMAGE HOSTS --}}
        @if($activeSection === 'standard')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

            {{-- Dump.li --}}
            <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800/80 shadow-sm p-5 space-y-4" x-init="setupDropzone('dumpli-input')">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-zinc-800 dark:text-zinc-100">Dump.li</h3>
                        <p class="text-xs text-zinc-400 mt-0.5">Up to 20 MB · 30 files at once · Tor + Clearnet</p>
                    </div>
                    <span class="px-2 py-0.5 bg-emerald-500/10 border border-emerald-500/20 text-emerald-600 dark:text-emerald-400 text-[10px] font-bold rounded-full uppercase">Recommended</span>
                </div>
                <form method="POST" enctype="multipart/form-data" rel="noreferrer" target="_blank">
                    <input type="hidden" name="name">
                    <input type="hidden" name="key">
                    <label class="file-drop-zone flex flex-col items-center justify-center border-2 border-dashed border-zinc-300 dark:border-zinc-700 rounded-xl p-5 cursor-pointer hover:border-violet-500 hover:bg-violet-50/30 dark:hover:bg-violet-900/10 transition has-file:border-emerald-500 dragging:border-violet-600 mb-3">
                        <flux:icon icon="photo" class="size-7 text-zinc-400 mb-2" />
                        <span class="file-name-label text-xs text-zinc-500 dark:text-zinc-400 text-center">Drop images here (PNG, JPG, GIF, WebP)</span>
                        <input id="dumpli-input" type="file" name="uploads[]" accept=".png,.jpg,.jpeg,.gif,.webp" multiple required class="hidden">
                    </label>
                    <div class="grid grid-cols-2 gap-2 mb-2">
                        <select name="expires" class="px-3 py-2 rounded-xl border border-zinc-300 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 text-xs text-zinc-700 dark:text-zinc-300">
                            <option value="3600">Expires: 1 hour</option>
                            <option value="86400">Expires: 1 day</option>
                            <option value="604800">Expires: 1 week</option>
                            <option value="2419200">Expires: 1 month</option>
                            <option value="31536000" selected>Expires: 1 year</option>
                            <option value="0">Never expires</option>
                        </select>
                        <div class="flex items-center gap-3 text-xs text-zinc-500 dark:text-zinc-400">
                            <label class="flex items-center gap-1.5 cursor-pointer">
                                <input type="checkbox" name="album" value="album" class="rounded"> Album
                            </label>
                            <label class="flex items-center gap-1.5 cursor-pointer">
                                <input type="checkbox" name="exif" value="exif" class="rounded"> Strip EXIF
                            </label>
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" formaction="https://dumpliwoard5qsrrsroni7bdiishealhky4snigbzfmzcquwo3kml4id.onion/upload/" class="flex-1 py-2 px-4 bg-zinc-800 hover:bg-zinc-700 text-white text-xs font-bold rounded-xl transition flex items-center justify-center gap-2">
                            <flux:icon icon="globe-alt" class="size-3.5" />
                            Tor Upload
                        </button>
                        <button type="submit" formaction="https://dump.li/upload/" class="flex-1 py-2 px-4 bg-violet-600 hover:bg-violet-700 text-white text-xs font-bold rounded-xl transition flex items-center justify-center gap-2">
                            <flux:icon icon="arrow-up-tray" class="size-3.5" />
                            Clearnet
                        </button>
                        <button type="reset" class="px-3 py-2 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-500 text-xs font-bold rounded-xl transition">✕</button>
                    </div>
                    <p class="text-[10px] text-zinc-400 mt-2">Replace <code class="font-mono">/image/</code> with <code class="font-mono">/image/get/</code> for direct link.</p>
                </form>
            </div>

            {{-- Zupimages --}}
            <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800/80 shadow-sm p-5 space-y-4" x-init="setupDropzone('zupimages-input')">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-zinc-800 dark:text-zinc-100">Zupimages</h3>
                        <p class="text-xs text-zinc-400 mt-0.5">Up to 7 MB · Up to 20 images at once</p>
                    </div>
                    <span class="px-2 py-0.5 bg-zinc-500/10 border border-zinc-500/20 text-zinc-500 dark:text-zinc-400 text-[10px] font-bold rounded-full uppercase">7 MB</span>
                </div>
                <form action="https://www.zupimages.net/up.php" method="POST" enctype="multipart/form-data" rel="noreferrer" target="_blank">
                    <label class="file-drop-zone flex flex-col items-center justify-center border-2 border-dashed border-zinc-300 dark:border-zinc-700 rounded-xl p-5 cursor-pointer hover:border-violet-500 hover:bg-violet-50/30 dark:hover:bg-violet-900/10 transition has-file:border-emerald-500 dragging:border-violet-600 mb-3">
                        <flux:icon icon="photo" class="size-7 text-zinc-400 mb-2" />
                        <span class="file-name-label text-xs text-zinc-500 dark:text-zinc-400 text-center">JPEG, PNG, GIF (up to 20 images)</span>
                        <input id="zupimages-input" type="file" name="files[]" accept=".jpeg,.jpg,.png,.gif" multiple required class="hidden">
                    </label>
                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 py-2 px-4 bg-violet-600 hover:bg-violet-700 text-white text-xs font-bold rounded-xl transition flex items-center justify-center gap-2">
                            <flux:icon icon="arrow-up-tray" class="size-3.5" />
                            Upload to Zupimages
                        </button>
                        <button type="reset" class="px-3 py-2 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-500 text-xs font-bold rounded-xl transition">✕</button>
                    </div>
                    <p class="text-[10px] text-zinc-400 mt-2">After upload: copy the "Lien direct de votre image" link.</p>
                </form>
            </div>

            {{-- Xxfot --}}
            <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800/80 shadow-sm p-5 space-y-4" x-init="setupDropzone('xxfot-input')">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-zinc-800 dark:text-zinc-100">Xxfot</h3>
                        <p class="text-xs text-zinc-400 mt-0.5">Up to 256 MB · Clearnet</p>
                    </div>
                    <span class="px-2 py-0.5 bg-blue-500/10 border border-blue-500/20 text-blue-600 dark:text-blue-400 text-[10px] font-bold rounded-full uppercase">256 MB</span>
                </div>
                <form action="https://xxfot.com/upload-image.php" method="POST" enctype="multipart/form-data" rel="noreferrer" target="_blank">
                    <label class="file-drop-zone flex flex-col items-center justify-center border-2 border-dashed border-zinc-300 dark:border-zinc-700 rounded-xl p-5 cursor-pointer hover:border-violet-500 hover:bg-violet-50/30 dark:hover:bg-violet-900/10 transition has-file:border-emerald-500 dragging:border-violet-600 mb-3">
                        <flux:icon icon="photo" class="size-7 text-zinc-400 mb-2" />
                        <span class="file-name-label text-xs text-zinc-500 dark:text-zinc-400 text-center">Any image format including AVIF</span>
                        <input id="xxfot-input" type="file" name="file" accept=".avif,image/*" required class="hidden">
                    </label>
                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 py-2 px-4 bg-violet-600 hover:bg-violet-700 text-white text-xs font-bold rounded-xl transition flex items-center justify-center gap-2">
                            <flux:icon icon="arrow-up-tray" class="size-3.5" />
                            Upload to Xxfot
                        </button>
                        <button type="reset" class="px-3 py-2 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-500 text-xs font-bold rounded-xl transition">✕</button>
                    </div>
                    <p class="text-[10px] text-zinc-400 mt-2">Accessible at <code class="font-mono">https://xxfot.com/&lt;id&gt;</code></p>
                </form>
            </div>

            {{-- SecureShare (onion) --}}
            <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800/80 shadow-sm p-5 space-y-4" x-init="setupDropzone('secureshare-input')">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-zinc-800 dark:text-zinc-100">SecureShare</h3>
                        <p class="text-xs text-zinc-400 mt-0.5">Onion · Up to 5 GB · Images & Files</p>
                    </div>
                    <span class="px-2 py-0.5 bg-zinc-800 border border-zinc-700 text-zinc-400 text-[10px] font-bold rounded-full uppercase">Tor Only</span>
                </div>
                <form method="POST" enctype="multipart/form-data" rel="noreferrer" target="_blank">
                    <label class="file-drop-zone flex flex-col items-center justify-center border-2 border-dashed border-zinc-300 dark:border-zinc-700 rounded-xl p-5 cursor-pointer hover:border-violet-500 hover:bg-violet-50/30 dark:hover:bg-violet-900/10 transition has-file:border-emerald-500 dragging:border-violet-600 mb-3">
                        <flux:icon icon="photo" class="size-7 text-zinc-400 mb-2" />
                        <span class="file-name-label text-xs text-zinc-500 dark:text-zinc-400 text-center">Images, videos, archives (Tor Browser)</span>
                        <input id="secureshare-input" type="file" name="files" accept=".avif,video/*,image/*,.zip,.rar,.7z,.tar,.gz" multiple required class="hidden">
                    </label>
                    <div class="flex gap-2">
                        <button type="submit" formaction="http://sqqmgg6xmhootsytdqs2fmkonwlqfqayubgp4ksqz4vrerxyhuplf4ad.onion/upload" class="flex-1 py-2 px-4 bg-zinc-800 hover:bg-zinc-700 text-white text-xs font-bold rounded-xl transition flex items-center justify-center gap-2">
                            <flux:icon icon="globe-alt" class="size-3.5" />
                            Mirror 1 (Tor)
                        </button>
                        <button type="submit" formaction="http://joi35hjuh4xz4rwshfhlmq5oxci2qu74kjudwa3klm6qvikpgduukgad.onion/upload" class="py-2 px-3 bg-zinc-700 hover:bg-zinc-600 text-white text-xs font-bold rounded-xl transition">M2</button>
                        <button type="reset" class="px-3 py-2 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-500 text-xs font-bold rounded-xl transition">✕</button>
                    </div>
                </form>
            </div>

        </div>
        @endif

        {{-- SHORT RETENTION IMAGE HOSTS --}}
        @if($activeSection === 'short')
        <div class="mb-4 p-3 rounded-xl bg-red-500/10 border border-red-500/20 flex items-start gap-2">
            <flux:icon icon="exclamation-triangle" class="size-4 text-red-500 shrink-0 mt-0.5" />
            <p class="text-xs text-red-700 dark:text-red-400">These image hosts have limited retention. Use for temporary sharing only.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

            {{-- Sxcu --}}
            <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800/80 shadow-sm p-5 space-y-4" x-init="setupDropzone('sxcu-input')">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-zinc-800 dark:text-zinc-100">Sxcu</h3>
                        <p class="text-xs text-zinc-400 mt-0.5">Up to 95 MB · Multiple mirrors available</p>
                    </div>
                    <span class="px-2 py-0.5 bg-zinc-500/10 border border-zinc-500/20 text-zinc-500 dark:text-zinc-400 text-[10px] font-bold rounded-full uppercase">95 MB</span>
                </div>
                <form method="POST" enctype="multipart/form-data" rel="noreferrer" target="_blank">
                    <label class="file-drop-zone flex flex-col items-center justify-center border-2 border-dashed border-zinc-300 dark:border-zinc-700 rounded-xl p-5 cursor-pointer hover:border-violet-500 hover:bg-violet-50/30 dark:hover:bg-violet-900/10 transition has-file:border-emerald-500 dragging:border-violet-600 mb-3">
                        <flux:icon icon="photo" class="size-7 text-zinc-400 mb-2" />
                        <span class="file-name-label text-xs text-zinc-500 dark:text-zinc-400 text-center">PNG, GIF, JPEG, ICO, BMP, WebP, WebM</span>
                        <input id="sxcu-input" type="file" name="file" accept=".png,.gif,.jpeg,.jpg,.ico,.bmp,.tiff,.webm,.webp" required class="hidden">
                    </label>
                    <div class="flex flex-wrap gap-2">
                        <button type="submit" formaction="https://sxcu.net/api/files/create" class="flex-1 py-2 px-3 bg-violet-600 hover:bg-violet-700 text-white text-xs font-bold rounded-xl transition flex items-center justify-center gap-1.5">
                            <flux:icon icon="arrow-up-tray" class="size-3.5" />
                            sxcu.net
                        </button>
                        <button type="submit" formaction="https://shx.gg/api/files/create" class="py-2 px-3 bg-zinc-700 hover:bg-zinc-600 text-white text-xs font-bold rounded-xl transition">shx.gg</button>
                        <button type="submit" formaction="https://questionable.link/api/files/create" class="py-2 px-3 bg-zinc-700 hover:bg-zinc-600 text-white text-xs font-bold rounded-xl transition">questionable</button>
                        <button type="reset" class="px-3 py-2 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-500 text-xs font-bold rounded-xl transition">✕</button>
                    </div>
                </form>
            </div>

            {{-- NinjaBox --}}
            <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800/80 shadow-sm p-5 space-y-4" x-init="setupDropzone('ninjabox-input')">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-zinc-800 dark:text-zinc-100">NinjaBox</h3>
                        <p class="text-xs text-zinc-400 mt-0.5">Up to 50 MB · Up to 20 files · Tor + Clearnet</p>
                    </div>
                    <span class="px-2 py-0.5 bg-zinc-500/10 border border-zinc-500/20 text-zinc-500 dark:text-zinc-400 text-[10px] font-bold rounded-full uppercase">50 MB</span>
                </div>
                <form method="POST" enctype="multipart/form-data" rel="noreferrer" target="_blank">
                    <input type="hidden" name="password">
                    <input type="hidden" name="quantity">
                    <label class="file-drop-zone flex flex-col items-center justify-center border-2 border-dashed border-zinc-300 dark:border-zinc-700 rounded-xl p-5 cursor-pointer hover:border-violet-500 hover:bg-violet-50/30 dark:hover:bg-violet-900/10 transition has-file:border-emerald-500 dragging:border-violet-600 mb-3">
                        <flux:icon icon="photo" class="size-7 text-zinc-400 mb-2" />
                        <span class="file-name-label text-xs text-zinc-500 dark:text-zinc-400 text-center">Images, videos, audio (up to 20 files)</span>
                        <input id="ninjabox-input" type="file" name="files" accept="image/*,video/*,audio/*" multiple required class="hidden">
                    </label>
                    <div class="flex gap-2">
                        <button type="submit" formaction="https://ninjabox.org/put" class="flex-1 py-2 px-4 bg-violet-600 hover:bg-violet-700 text-white text-xs font-bold rounded-xl transition flex items-center justify-center gap-2">
                            <flux:icon icon="arrow-up-tray" class="size-3.5" />
                            Clearnet
                        </button>
                        <button type="submit" formaction="http://ninjabox47e4nlw3ex6dajrxxvne2ysjcfitcqakszysp6zob6sxrqad.onion/put" class="py-2 px-3 bg-zinc-800 hover:bg-zinc-700 text-white text-xs font-bold rounded-xl transition">Tor</button>
                        <button type="reset" class="px-3 py-2 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-500 text-xs font-bold rounded-xl transition">✕</button>
                    </div>
                </form>
            </div>

            {{-- CloudSafe (onion) --}}
            <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800/80 shadow-sm p-5 space-y-4" x-init="setupDropzone('cloudsafe-input')">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-zinc-800 dark:text-zinc-100">CloudSafe</h3>
                        <p class="text-xs text-zinc-400 mt-0.5">Onion · Up to 10 MB · Configurable expiry · 750 files at once</p>
                    </div>
                    <span class="px-2 py-0.5 bg-zinc-800 border border-zinc-700 text-zinc-400 text-[10px] font-bold rounded-full uppercase">Tor Only</span>
                </div>
                <form method="POST" enctype="multipart/form-data" rel="noreferrer" target="_blank">
                    <label class="file-drop-zone flex flex-col items-center justify-center border-2 border-dashed border-zinc-300 dark:border-zinc-700 rounded-xl p-5 cursor-pointer hover:border-violet-500 hover:bg-violet-50/30 dark:hover:bg-violet-900/10 transition has-file:border-emerald-500 dragging:border-violet-600 mb-3">
                        <flux:icon icon="photo" class="size-7 text-zinc-400 mb-2" />
                        <span class="file-name-label text-xs text-zinc-500 dark:text-zinc-400 text-center">Any image format (Tor Browser)</span>
                        <input id="cloudsafe-input" type="file" name="file" accept=".avif,image/*" required class="hidden">
                    </label>
                    <div class="flex flex-col gap-2">
                        <select name="expiry" class="w-full px-3 py-2 rounded-xl border border-zinc-300 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 text-xs text-zinc-700 dark:text-zinc-300">
                            <option value="1h">Expires: 1 hour</option>
                            <option value="1d">Expires: 1 day</option>
                            <option value="1w">Expires: 1 week</option>
                            <option value="never" selected>Never expires</option>
                        </select>
                        <div class="flex gap-2">
                            <button type="submit" formaction="http://3ozqtdtygjbxewsfxzemfkhdjptmg34ggv2ra6jvuz6sz6ostbuyxbad.onion/index.php" class="flex-1 py-2 px-4 bg-zinc-800 hover:bg-zinc-700 text-white text-xs font-bold rounded-xl transition flex items-center justify-center gap-2">
                                <flux:icon icon="arrow-up-tray" class="size-3.5" />
                                Mirror 1 (Tor)
                            </button>
                            <button type="submit" formaction="http://7bkh7thfbtlsn3e2popboprokfwj254dax42xzyvimnkk4dbiklgg3yd.onion/index.php" class="py-2 px-3 bg-zinc-700 hover:bg-zinc-600 text-white text-xs font-bold rounded-xl transition">M2</button>
                            <button type="reset" class="px-3 py-2 bg-zinc-100 dark:bg-zinc-800 hover:bg-zinc-200 dark:hover:bg-zinc-700 text-zinc-500 text-xs font-bold rounded-xl transition">✕</button>
                        </div>
                    </div>
                </form>
            </div>

        </div>
        @endif

    </div>
    @endif

</div>
