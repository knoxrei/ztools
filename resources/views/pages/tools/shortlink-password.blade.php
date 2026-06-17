<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Encrypted Link - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css'])
    
    @if($cloak_title)
    <meta property="og:title" content="{{ $cloak_title }}">
    @endif
    @if($cloak_desc)
    <meta property="og:description" content="{{ $cloak_desc }}">
    @endif
</head>
<body class="min-h-screen bg-zinc-50 dark:bg-zinc-950 flex items-center justify-center p-6 text-zinc-900 dark:text-zinc-100 antialiased">
    <div class="w-full max-w-md bg-white dark:bg-zinc-900 rounded-3xl border border-zinc-200 dark:border-zinc-800 shadow-xl p-8 space-y-6">
        
        <div class="text-center space-y-2">
            <div class="inline-flex p-3 bg-violet-50 dark:bg-violet-900/30 rounded-2xl text-violet-600 dark:text-violet-400">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-8 h-8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                </svg>
            </div>
            <h1 class="text-xl font-bold tracking-tight">Enter Password to Unlock</h1>
            <p class="text-xs text-zinc-500 dark:text-zinc-400">
                This short link is encrypted and password-protected.
            </p>
        </div>

        @if($error)
        <div class="p-3 bg-red-500/10 border border-red-500/20 text-red-600 dark:text-red-400 text-xs rounded-xl text-center font-medium">
            {{ $error }}
        </div>
        @endif

        <form action="{{ route('short-link.redirect', ['code' => $code]) }}" method="GET" class="space-y-4">
            <div class="space-y-1">
                <label for="password" class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-widest">Password</label>
                <input 
                    type="password" 
                    name="password" 
                    id="password" 
                    required 
                    placeholder="Enter link password..." 
                    class="w-full px-4 py-2.5 bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-violet-500 dark:focus:ring-violet-400 transition font-mono"
                    autofocus
                />
            </div>

            <button type="submit" class="w-full py-2.5 bg-violet-600 hover:bg-violet-700 text-white font-bold rounded-xl text-sm shadow transition">
                Unlock & Proceed
            </button>
        </form>

        <div class="text-center">
            <span class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wider">SECURE SHIELDED REDIRECT</span>
        </div>
    </div>
</body>
</html>
