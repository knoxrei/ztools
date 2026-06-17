<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title> {{ $title ?? 'Home' }} - {{ config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600&display=swap" rel="stylesheet" />
    @livewireStyles
    @fluxAppearance

</head>

<body class="min-h-screen bg-white dark:bg-zinc-900 antialiased">
    <flux:header container class="bg-zinc-50 fixed z-100 w-full  dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-700">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" />

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