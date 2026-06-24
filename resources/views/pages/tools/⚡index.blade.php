<?php

use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Tools Suite')] class extends Component
{
    //
};
?>

<div class="min-h-screen pb-16 space-y-8">
    
    {{-- Page Header --}}
    <div>
        <div class="flex items-center gap-3 mb-2">
            <div class="p-2">
                <flux:icon icon="square-3-stack-3d" class="size-7 text-violet-600 dark:text-violet-400" />
            </div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white tracking-tight">Security & Cryptography Suite</h1>
        </div>
        <p class="text-sm text-zinc-500 dark:text-zinc-400 ml-14">
            Select a specialized utility card below to launch the corresponding secure offline sandbox tool.
        </p>
    </div>

    {{-- Tools Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

        {{-- 1. Symmetric Encryption --}}
        <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800/80 shadow-sm p-5 flex flex-col justify-between space-y-4 hover:border-zinc-300 dark:hover:border-zinc-700 transition">
            <div class="space-y-3">
                <div class="flex items-center gap-2">
                    <div class="p-1.5 rounded-lg bg-violet-50 dark:bg-violet-900/30 text-violet-600 dark:text-violet-400">
                        <flux:icon icon="lock-closed" class="size-4" />
                    </div>
                    <h3 class="text-xs font-bold text-zinc-700 dark:text-zinc-200 uppercase tracking-widest">Symmetric Encryption</h3>
                </div>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 leading-normal">
                    Encrypt plaintext payloads locally using robust ciphers such as AES-256-CBC, ChaCha20, Twofish, Blowfish, 3DES, or RC4.
                </p>
            </div>
            <flux:button :href="route('encryption')" variant="ghost" size="sm" class="w-full text-violet-600 hover:text-white dark:text-violet-400 hover:bg-violet-600 rounded-xl" wire:navigate>
                Launch Encryptor
            </flux:button>
        </div>

        {{-- 2. Symmetric Decryption --}}
        <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800/80 shadow-sm p-5 flex flex-col justify-between space-y-4 hover:border-zinc-300 dark:hover:border-zinc-700 transition">
            <div class="space-y-3">
                <div class="flex items-center gap-2">
                    <div class="p-1.5 rounded-lg bg-violet-50 dark:bg-violet-900/30 text-violet-600 dark:text-violet-400">
                        <flux:icon icon="lock-open" class="size-4" />
                    </div>
                    <h3 class="text-xs font-bold text-zinc-700 dark:text-zinc-200 uppercase tracking-widest">Symmetric Decryption</h3>
                </div>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 leading-normal">
                    Decrypt ciphertext back into plaintext. Configured with keyphrase alignment and encoding detection.
                </p>
            </div>
            <flux:button :href="route('decryption')" variant="ghost" size="sm" class="w-full text-violet-600 hover:text-white dark:text-violet-400 hover:bg-violet-600 rounded-xl" wire:navigate>
                Launch Decryptor
            </flux:button>
        </div>

        {{-- 3. Hash & Checksum --}}
        <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800/80 shadow-sm p-5 flex flex-col justify-between space-y-4 hover:border-zinc-300 dark:hover:border-zinc-700 transition">
            <div class="space-y-3">
                <div class="flex items-center gap-2">
                    <div class="p-1.5 rounded-lg bg-violet-50 dark:bg-violet-900/30 text-violet-600 dark:text-violet-400">
                        <flux:icon icon="hashtag" class="size-4" />
                    </div>
                    <h3 class="text-xs font-bold text-zinc-700 dark:text-zinc-200 uppercase tracking-widest">Hash & Checksum</h3>
                </div>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 leading-normal">
                    Generate MD5, SHA1, SHA256, SHA512 hashes. Compute checksum hashes for uploaded files and run checksum matching diagnostics.
                </p>
            </div>
            <flux:button :href="route('hash-checksum')" variant="ghost" size="sm" class="w-full text-violet-600 hover:text-white dark:text-violet-400 hover:bg-violet-600 rounded-xl" wire:navigate>
                Launch Hash Generator
            </flux:button>
        </div>

        {{-- 4. File Forensics --}}
        <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800/80 shadow-sm p-5 flex flex-col justify-between space-y-4 hover:border-zinc-300 dark:hover:border-zinc-700 transition">
            <div class="space-y-3">
                <div class="flex items-center gap-2">
                    <div class="p-1.5 rounded-lg bg-violet-50 dark:bg-violet-900/30 text-violet-600 dark:text-violet-400">
                        <flux:icon icon="eye" class="size-4" />
                    </div>
                    <h3 class="text-xs font-bold text-zinc-700 dark:text-zinc-200 uppercase tracking-widest">File Forensics</h3>
                </div>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 leading-normal">
                    Inspect PDF/image metadata streams, detect spoofed signatures (magic bytes diagnostics), and query Raw Exif tag tables.
                </p>
            </div>
            <flux:button :href="route('forensics')" variant="ghost" size="sm" class="w-full text-violet-600 hover:text-white dark:text-violet-400 hover:bg-violet-600 rounded-xl" wire:navigate>
                Launch Forensic Analyzer
            </flux:button>
        </div>

        {{-- 5. QR Code --}}
        <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800/80 shadow-sm p-5 flex flex-col justify-between space-y-4 hover:border-zinc-300 dark:hover:border-zinc-700 transition">
            <div class="space-y-3">
                <div class="flex items-center gap-2">
                    <div class="p-1.5 rounded-lg bg-violet-50 dark:bg-violet-900/30 text-violet-600 dark:text-violet-400">
                        <flux:icon icon="qr-code" class="size-4" />
                    </div>
                    <h3 class="text-xs font-bold text-zinc-700 dark:text-zinc-200 uppercase tracking-widest">QR Code</h3>
                </div>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 leading-normal">
                    Generate and customize clean QR codes for plaintext, secure WiFi config, contact VCards, email templates, and SMS payloads.
                </p>
            </div>
            <flux:button :href="route('qrcode')" variant="ghost" size="sm" class="w-full text-violet-600 hover:text-white dark:text-violet-400 hover:bg-violet-600 rounded-xl" wire:navigate>
                Launch QR Generator
            </flux:button>
        </div>

        {{-- 6. Fake Identity --}}
        <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800/80 shadow-sm p-5 flex flex-col justify-between space-y-4 hover:border-zinc-300 dark:hover:border-zinc-700 transition">
            <div class="space-y-3">
                <div class="flex items-center gap-2">
                    <div class="p-1.5 rounded-lg bg-violet-50 dark:bg-violet-900/30 text-violet-600 dark:text-violet-400">
                        <flux:icon icon="identification" class="size-4" />
                    </div>
                    <h3 class="text-xs font-bold text-zinc-700 dark:text-zinc-200 uppercase tracking-widest">Fake Identity</h3>
                </div>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 leading-normal">
                    Generate secure fake profiles/personae (addresses, phone numbers, credit card tokens) for sandbox testing.
                </p>
            </div>
            <flux:button :href="route('fake-identity')" variant="ghost" size="sm" class="w-full text-violet-600 hover:text-white dark:text-violet-400 hover:bg-violet-600 rounded-xl" wire:navigate>
                Launch Profile Generator
            </flux:button>
        </div>

        {{-- 7. Short Link --}}
        <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800/80 shadow-sm p-5 flex flex-col justify-between space-y-4 hover:border-zinc-300 dark:hover:border-zinc-700 transition lg:col-span-1">
            <div class="space-y-3">
                <div class="flex items-center gap-2">
                    <div class="p-1.5 rounded-lg bg-violet-50 dark:bg-violet-900/30 text-violet-600 dark:text-violet-400">
                        <flux:icon icon="link" class="size-4" />
                    </div>
                    <h3 class="text-xs font-bold text-zinc-700 dark:text-zinc-200 uppercase tracking-widest">Short Link & Cloaker</h3>
                </div>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 leading-normal">
                    Secure redirections behind a password, add expiration triggers, configure burner modes, and spoof crawler preview tags.
                </p>
            </div>
            <flux:button :href="route('shortlink')" variant="ghost" size="sm" class="w-full text-violet-600 hover:text-white dark:text-violet-400 hover:bg-violet-600 rounded-xl" wire:navigate>
                Launch Link Shortener
            </flux:button>
        </div>

        {{-- 8. URL Checker --}}
        <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800/80 shadow-sm p-5 flex flex-col justify-between space-y-4 hover:border-zinc-300 dark:hover:border-zinc-700 transition">
            <div class="space-y-3">
                <div class="flex items-center gap-2">
                    <div class="p-1.5 rounded-lg bg-violet-50 dark:bg-violet-900/30 text-violet-600 dark:text-violet-400">
                        <flux:icon icon="shield-check" class="size-4" />
                    </div>
                    <h3 class="text-xs font-bold text-zinc-700 dark:text-zinc-200 uppercase tracking-widest">URL Safety Checker</h3>
                </div>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 leading-normal">
                    Inspect URLs for potential phishing indicators, tracking query parameters, insecure protocols, and verify connection status on both Clearnet and Tor.
                </p>
            </div>
            <flux:button :href="route('url-checker')" variant="ghost" size="sm" class="w-full text-violet-600 hover:text-white dark:text-violet-400 hover:bg-violet-600 rounded-xl" wire:navigate>
                Launch URL Checker
            </flux:button>
        </div>

        {{-- 9. Anonymous File Upload --}}
        <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 shadow-sm p-5 flex flex-col justify-between space-y-4 hover:border-zinc-300 dark:hover:border-zinc-700 transition">
            <div class="space-y-3">
                <div class="flex items-center gap-2">
                    <div class="p-1.5 rounded-lg bg-violet-50 dark:bg-violet-900/30 text-violet-600 dark:text-violet-400">
                        <flux:icon icon="arrow-up-tray" class="size-4" />
                    </div>
                    <h3 class="text-xs font-bold text-zinc-700 dark:text-zinc-200 uppercase tracking-widest">Anonymous File Upload</h3>
                </div>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 leading-normal">
                    Upload files and images anonymously to external hosting services (Gofile, Anonfile, Dump.li, and more). No account required. Onion & clearnet mirrors available.
                </p>
            </div>
            <flux:button :href="route('filehost')" variant="ghost" size="sm" class="w-full text-violet-600 hover:text-white dark:text-violet-400 hover:bg-violet-600 rounded-xl" wire:navigate>
                Launch File Upload
            </flux:button>
        </div>

        {{-- 10. Multi Search & Gateways --}}
        <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800/80 shadow-sm p-5 flex flex-col justify-between space-y-4 hover:border-zinc-300 dark:hover:border-zinc-700 transition">
            <div class="space-y-3">
                <div class="flex items-center gap-2">
                    <div class="p-1.5 rounded-lg bg-violet-50 dark:bg-violet-900/30 text-violet-600 dark:text-violet-400">
                        <flux:icon icon="magnifying-glass" class="size-4" />
                    </div>
                    <h3 class="text-xs font-bold text-zinc-700 dark:text-zinc-200 uppercase tracking-widest">Multi Search & Gateways</h3>
                </div>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 leading-normal">
                    Search anonymously across translators, socials, scientific libraries, crypto explorers, and Tor onion search directories via integrated gateways.
                </p>
            </div>
            <flux:button :href="route('services')" variant="ghost" size="sm" class="w-full text-violet-600 hover:text-white dark:text-violet-400 hover:bg-violet-600 rounded-xl" wire:navigate>
                Launch Multi Search
            </flux:button>
        </div>
        {{-- 11. Instagram & Media Downloader --}}
        <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800/80 shadow-sm p-5 flex flex-col justify-between space-y-4 hover:border-zinc-300 dark:hover:border-zinc-700 transition">
            <div class="space-y-3">
                <div class="flex items-center gap-2">
                    <div class="p-1.5 rounded-lg bg-violet-50 dark:bg-violet-900/30 text-violet-600 dark:text-violet-400">
                        <flux:icon icon="arrow-down-tray" class="size-4" />
                    </div>
                    <h3 class="text-xs font-bold text-zinc-700 dark:text-zinc-200 uppercase tracking-widest">Media Downloader</h3>
                </div>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 leading-normal">
                    Download Instagram posts, reels, carousel images, and videos. Paste the post URL and retrieve media files directly.
                </p>
            </div>
            <flux:button :href="route('downloader')" variant="ghost" size="sm" class="w-full text-violet-600 hover:text-white dark:text-violet-400 hover:bg-violet-600 rounded-xl" wire:navigate>
                Launch Downloader
            </flux:button>
        </div>

    </div>

</div>
