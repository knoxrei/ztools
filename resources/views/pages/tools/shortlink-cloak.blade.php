<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $link->cloak_title ?? 'Redirecting...' }}</title>
    @vite(['resources/css/app.css'])

    @if($link->cloak_title)
    <meta property="og:title" content="{{ $link->cloak_title }}">
    @endif
    @if($link->cloak_desc)
    <meta property="og:description" content="{{ $link->cloak_desc }}">
    @endif
</head>
<body class="min-h-screen bg-zinc-50 dark:bg-zinc-950 flex items-center justify-center p-6 text-zinc-900 dark:text-zinc-100 antialiased">
    <div class="w-full max-w-md bg-white dark:bg-zinc-900 rounded-3xl border border-zinc-200 dark:border-zinc-800 shadow-xl p-8 space-y-6">
        
        <div class="text-center space-y-2">
            <div class="inline-flex p-3 bg-amber-50 dark:bg-amber-900/20 rounded-2xl text-amber-600 dark:text-amber-500">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-8 h-8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                </svg>
            </div>
            <h1 class="text-xl font-bold tracking-tight">You are leaving {{ config('app.name') }}</h1>
            <p class="text-xs text-zinc-500 dark:text-zinc-400 leading-relaxed">
                This link was created with a custom cloaking filter. You are about to proceed to the target destination.
            </p>
        </div>

        <div class="p-4 bg-zinc-50 dark:bg-zinc-800/50 rounded-2xl border border-zinc-200 dark:border-zinc-800 text-xs space-y-3">
            <div>
                <span class="block text-[9px] font-bold text-zinc-400 uppercase tracking-wider">Cloaked Metadata</span>
                <span class="block font-semibold text-zinc-700 dark:text-zinc-300 mt-0.5">{{ $link->cloak_title ?? 'None' }}</span>
                <span class="block text-zinc-500 mt-0.5">{{ $link->cloak_desc ?? 'No description provided.' }}</span>
            </div>
            
            <div class="pt-3 border-t border-zinc-200 dark:border-zinc-700">
                <span class="block text-[9px] font-bold text-zinc-400 uppercase tracking-wider">Destination Host</span>
                <span class="block font-mono text-zinc-800 dark:text-zinc-200 break-all mt-0.5">{{ parse_url($link->original_url, PHP_URL_HOST) }}</span>
            </div>
        </div>

        <form action="{{ route('short-link.redirect', ['code' => $code]) }}" method="GET" class="space-y-3">
            <input type="hidden" name="confirm" value="1" />
            @if($password)
            <input type="hidden" name="password" value="{{ $password }}" />
            @endif

            <button type="submit" class="w-full py-2.5 bg-violet-600 hover:bg-violet-700 text-white font-bold rounded-xl text-sm shadow transition">
                Proceed to Destination
            </button>
            
            <a href="/" class="block text-center py-2 text-zinc-500 hover:text-zinc-700 dark:hover:text-zinc-300 text-xs font-semibold">
                Cancel
            </a>
        </form>
    </div>
</body>
</html>
