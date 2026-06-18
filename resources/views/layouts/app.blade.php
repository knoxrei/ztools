@php
$routeName = request()->route()?->getName();

$seo = match ($routeName) {
'home' => [
'title' => 'Z-Knox - Advanced Cybersecurity & Encryption Tools Suite',
'description' => 'Z-Knox is a comprehensive, professional suite of advanced cryptographic, digital forensics, and privacy tools. Encrypt/decrypt payloads, generate secure QR codes, check URL safety, create cloaked short links, and perform forensic analysis.',
'keywords' => 'cybersecurity, encryption, decryption, cryptography, forensics, qr code generator, shortlink, url checker, online privacy, secure tools',
],
'encryption' => [
'title' => 'Symmetric Encryption Tool - Secure Payload Cryptography | Z-Knox',
'description' => 'Securely encrypt text payloads using advanced algorithms like AES-256, Blowfish, Twofish, ChaCha20, DES, Triple DES, and RC4. Set custom keys and initialization vectors (IV).',
'keywords' => 'symmetric encryption, aes-256-cbc, chacha20, blowfish, twofish, payload encryption, secure cryptography',
],
'decryption' => [
'title' => 'Symmetric Decryption Tool - Decrypt Ciphertexts Securely | Z-Knox',
'description' => 'Decrypt encrypted ciphertexts. Supports base64 and hex encoded ciphertexts encrypted with AES, Twofish, Blowfish, DES, 3DES, ChaCha20, and RC4.',
'keywords' => 'symmetric decryption, decrypt cipher, aes decryption, chacha20 decrypt, base64 decryption',
],
'hash-checksum' => [
'title' => 'Hash & Checksum Generator - Verify File Integrity | Z-Knox',
'description' => 'Calculate hash checksums for texts and uploaded files. Supports MD5, SHA-1, SHA-256, SHA-512, MurmurHash, and bcrypt verification.',
'keywords' => 'hash generator, file checksum, sha256 hash, md5 checksum, bcrypt verifier, file integrity',
],
'forensics' => [
'title' => 'Digital Forensics & Metadata Analyzer - Inspect Files | Z-Knox',
'description' => 'Analyze and inspect uploaded files for metadata, hidden EXIF data, hex structures, MIME types, and potential security analysis.',
'keywords' => 'digital forensics, metadata extraction, exif analyzer, hex viewer, document analysis, threat detection',
],
'qrcode' => [
'title' => 'Professional QR Code Generator - Custom & Secure QR Codes | Z-Knox',
'description' => 'Create custom QR codes with custom colors, size, margins, and formats (PNG, SVG). Presets for text, URL, Wi-Fi, email, SMS, phone, geo-coordinates, and vCards.',
'keywords' => 'qr code generator, custom qr code, wifi qr code, vcard qr code, svg qr code, secure qr codes',
],
'fake-identity' => [
'title' => 'Fake Identity Generator - Developer Testing Profiles | Z-Knox',
'description' => 'Generate complete, realistic dummy user profiles and fake identities for testing, QA, data seeding, and privacy protection.',
'keywords' => 'identity generator, dummy data, fake profile, user seeding, qa testing data, privacy profiles',
],
'shortlink' => [
'title' => 'Secure Short Link Creator - Cloaking & Tor Redirection | Z-Knox',
'description' => 'Shorten URLs with advanced features like password protection, click limits, expiry times, link cloaking, and Tor (.onion) target support.',
'keywords' => 'url shortener, secure shortlink, link cloaking, tor redirection, self-destruct link, password protected link',
],
'url-checker' => [
'title' => 'URL Safety & Redirect Checker - Analyze Web Links | Z-Knox',
'description' => 'Scan URLs for safety, inspect HTTP redirect headers, verify IP addresses, check online status, and trace redirect chains.',
'keywords' => 'url checker, redirect tracer, domain safety, link scanner, website status checker, threat intelligence',
],
'tools' => [
'title' => 'Tools Suite - Advanced Cyber & Privacy Tools | Z-Knox',
'description' => 'Explore the complete suite of advanced cybersecurity, privacy, and encryption utilities available on Z-Knox.',
'keywords' => 'cybersecurity suite, cryptography tools, network tools, developer utilities, privacy dashboard',
],
'support' => [
'title' => 'Support Z-Knox - Help Keep Our Privacy Tools Online',
'description' => 'Support Z-Knox and help keep advanced privacy, forensic, and cryptographic tools free and open source. We accept cryptocurrency donations.',
'keywords' => 'support privacy, donate crypto, monero donation, bitcoin support, open source funding',
],
'contact' => [
'title' => 'Contact Us - Z-Knox Security Support',
'description' => 'Get in touch with the Z-Knox administration and support team. Contact us securely via PGP email, Session messenger, or Telegram.',
'keywords' => 'contact support, secure chat, pgp support, session messenger, customer contact',
],
default => [
'title' => 'Z-Knox - Advanced Cybersecurity & Encryption Tools',
'description' => 'Z-Knox is a professional suite of advanced cryptographic, digital forensics, and privacy tools.',
'keywords' => 'cybersecurity, encryption, cryptography, forensics, secure tools',
]
};

$pageTitle = isset($title) ? $title . ' - ' . config('app.name') : $seo['title'];
$pageDescription = $seo['description'];
$pageKeywords = $seo['keywords'];
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $pageDescription }}">
    <meta name="keywords" content="{{ $pageKeywords }}">
    <meta name="robots" content="index, follow">

    <!-- Canonical URL -->
    <link rel="canonical" href="{{ request()->url() }}" />

    <!-- OpenGraph Metadata -->
    <meta property="og:title" content="{{ $pageTitle }}" />
    <meta property="og:description" content="{{ $pageDescription }}" />
    <meta property="og:type" content="website" />
    <meta property="og:url" content="{{ request()->url() }}" />
    <meta property="og:image" content="{{ asset('/logo.webp') }}" />
    <meta property="og:site_name" content="{{ config('app.name') }}" />

    <!-- Twitter Card Metadata -->
    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="{{ $pageTitle }}" />
    <meta name="twitter:description" content="{{ $pageDescription }}" />
    <meta name="twitter:image" content="{{ asset('/logo.webp') }}" />

    <!-- JSON-LD Structured Data for WebSite -->
    <script type="application/ld+json">
        {
            "@@context": "https://schema.org",
            "@@type": "WebSite",
            "name": "{{ config('app.name') }}",
            "url": "{{ url('/') }}",
            "potentialAction": {
                "@@type": "SearchAction",
                "target": "{{ url('/tools') }}?search={search_term_string}",
                "query-input": "required name=search_term_string"
            }
        }
    </script>

    <!-- JSON-LD Structured Data for WebApplication -->
    @if(in_array($routeName, ['encryption', 'decryption', 'hash-checksum', 'forensics', 'qrcode', 'fake-identity', 'shortlink', 'url-checker']))
    <script type="application/ld+json">
        {
            "@@context": "https://schema.org",
            "@@type": "WebApplication",
            "name": "{{ $pageTitle }}",
            "url": "{{ request()->url() }}",
            "description": "{{ $pageDescription }}",
            "applicationCategory": "DeveloperApplication",
            "operatingSystem": "All"
        }
    </script>
    @endif

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600&display=swap" rel="stylesheet" />
    @livewireStyles
    @fluxAppearance
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <script src="http://admate3tczgp6digew7jpzcosq52rs7anru53imwqimron27emq7dbqd.onion/js/get-banners.js" defer></script>
</head>

<body x-data x-init="$flux.appearance = 'dark'" class="min-h-screen bg-white dark:bg-zinc-900 antialiased">
    <flux:header container class="bg-zinc-50 fixed z-100 w-full  dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-700">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" />
        <div class="flex items-center w-full ">

            <span class="flex w-full font-bold text-white ">{{ config('app.name') }}</span>
        </div>
        <flux:navbar class="w-full max-lg:hidden justify-end ">
            <flux:navbar.item :href="route('home')" icon="home" :current="request()->routeIs('home')" wire:navigate>Home</flux:navbar.item>
            <flux:navbar.item :href="route('tools')" icon="square-3-stack-3d" :current="request()->routeIs('tools')" wire:navigate>Tools</flux:navbar.item>
            <flux:navbar.item :href="route('support')" icon="heart" :current="request()->routeIs('support')" wire:navigate>Support Us</flux:navbar.item>
            <flux:navbar.item :href="route('contact')" icon="chat-bubble-left-right" :current="request()->routeIs('contact')" wire:navigate>Contact Us</flux:navbar.item>
        </flux:navbar>

    </flux:header>

    <div class="pt-14">
        <flux:sidebar sticky collapsible="mobile" class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">

            <flux:sidebar.collapse class="lg:hidden" />

            <flux:sidebar.nav>
                <flux:sidebar.item :href="route('home')" icon="home" :current="request()->routeIs('home')" wire:navigate>Home</flux:sidebar.item>
                <flux:sidebar.item :href="route('tools')" icon="square-3-stack-3d" :current="request()->routeIs('tools')" wire:navigate>All Tools</flux:sidebar.item>
                <flux:sidebar.item :href="route('support')" icon="heart" :current="request()->routeIs('support')" wire:navigate>Support Us</flux:sidebar.item>
                <flux:sidebar.item :href="route('contact')" icon="chat-bubble-left-right" :current="request()->routeIs('contact')" wire:navigate>Contact Us</flux:sidebar.item>



                <flux:sidebar.group heading="Tools Suite" class="grid mt-4">
                    <flux:sidebar.item icon="lock-closed" :href="route('encryption')" :current="request()->routeIs('encryption')" wire:navigate>
                        {{ __('Encryption') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="lock-open" :href="route('decryption')" :current="request()->routeIs('decryption')" wire:navigate>
                        {{ __('Decryption') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="hashtag" :href="route('hash-checksum')" :current="request()->routeIs('hash-checksum')" wire:navigate>
                        {{ __('Hash & Checksum') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="eye" :href="route('forensics')" :current="request()->routeIs('forensics')" wire:navigate>
                        {{ __('Forensics') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="qr-code" :href="route('qrcode')" :current="request()->routeIs('qrcode')" wire:navigate>
                        {{ __('QR Code') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="identification" :href="route('fake-identity')" :current="request()->routeIs('fake-identity')" wire:navigate>
                        {{ __('Fake Identity') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="link" :href="route('shortlink')" :current="request()->routeIs('shortlink')" wire:navigate>
                        {{ __('Short Link') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="shield-check" :href="route('url-checker')" :current="request()->routeIs('url-checker')" wire:navigate>
                        {{ __('URL Checker') }}
                    </flux:sidebar.item>


                </flux:sidebar.group>
            </flux:sidebar.nav>
        </flux:sidebar>

        <flux:main container>
            {{ $slot }}
        </flux:main>
    </div>

    @livewireScripts

    @fluxScripts
</body>


</html>