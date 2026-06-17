<?php

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Title;

new #[Title('Hash & Checksum')] class extends Component
{
    use WithFileUploads;

    // Tab state
    public string $activeTab = 'text'; // text | file | compare

    // ── Text Hash Properties ──
    public string $textToHash = '';

    // ── File Hash Properties ──
    public $uploadedFile;
    public string $fileMd5 = '';
    public string $fileSha256 = '';
    public string $fileSha512 = '';
    public array $fileMeta = [];

    // ── Compare Properties ──
    public string $hashA = '';
    public string $hashB = '';
    public ?bool $compareResult = null;

    public function updatedTextToHash(): void
    {
        // Reactive update for text hashing
    }

    public function updatedUploadedFile(): void
    {
        $this->validate([
            'uploadedFile' => 'required|file|max:51200', // Limit 50MB
        ]);

        try {
            $path = $this->uploadedFile->getRealPath();
            
            $this->fileMd5 = hash_file('md5', $path);
            $this->fileSha256 = hash_file('sha256', $path);
            $this->fileSha512 = hash_file('sha512', $path);

            $this->fileMeta = [
                'name' => $this->uploadedFile->getClientOriginalName(),
                'size' => $this->formatSize($this->uploadedFile->getSize()),
                'mime' => $this->uploadedFile->getMimeType(),
            ];
        } catch (Throwable $e) {
            $this->addError('uploadedFile', 'Failed to process file: ' . $e->getMessage());
        }
    }

    public function updatedHashA(): void
    {
        $this->runCompare();
    }

    public function updatedHashB(): void
    {
        $this->runCompare();
    }

    private function runCompare(): void
    {
        $cleanA = strtolower(trim($this->hashA));
        $cleanB = strtolower(trim($this->hashB));

        if ($cleanA === '' || $cleanB === '') {
            $this->compareResult = null;
            return;
        }

        $this->compareResult = ($cleanA === $cleanB);
    }

    public function getHashes(): array
    {
        if ($this->textToHash === '') {
            return [
                'MD5' => '',
                'SHA-1' => '',
                'SHA-224' => '',
                'SHA-256' => '',
                'SHA-384' => '',
                'SHA-512' => '',
                'SHA3-224' => '',
                'SHA3-256' => '',
                'SHA3-384' => '',
                'SHA3-512' => '',
            ];
        }

        return [
            'MD5' => hash('md5', $this->textToHash),
            'SHA-1' => hash('sha1', $this->textToHash),
            'SHA-224' => hash('sha224', $this->textToHash),
            'SHA-256' => hash('sha256', $this->textToHash),
            'SHA-384' => hash('sha384', $this->textToHash),
            'SHA-512' => hash('sha512', $this->textToHash),
            'SHA3-224' => hash('sha3-224', $this->textToHash),
            'SHA3-256' => hash('sha3-256', $this->textToHash),
            'SHA3-384' => hash('sha3-384', $this->textToHash),
            'SHA3-512' => hash('sha3-512', $this->textToHash),
        ];
    }

    public function clearText(): void
    {
        $this->reset('textToHash');
    }

    public function clearFile(): void
    {
        $this->reset(['uploadedFile', 'fileMd5', 'fileSha256', 'fileSha512', 'fileMeta']);
        $this->resetErrorBag('uploadedFile');
    }

    public function clearCompare(): void
    {
        $this->reset(['hashA', 'hashB', 'compareResult']);
    }

    private function formatSize(int $bytes): string
    {
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        }
        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        }
        return $bytes . ' Bytes';
    }
};
?>

<div class="min-h-screen pb-12">

    {{-- Page Header --}}
    <div class="mb-8">
        <div class="flex items-center gap-3 mb-2">
            <div class="p-2">
                <flux:icon icon="hashtag" class="size-7 text-violet-600 dark:text-violet-400" />
            </div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white tracking-tight">Hash & Checksum Tools</h1>
        </div>
        <p class="text-sm text-zinc-500 dark:text-zinc-400 ml-14">
            Generate text hashes, calculate file checksums, and perform real-time hash comparisons.
        </p>
    </div>

    {{-- Tabs Navigation --}}
    <div class="mb-7">
        <div class="flex flex-wrap gap-1 p-1.5 bg-zinc-100 dark:bg-zinc-800/80 rounded-2xl border border-zinc-200 dark:border-zinc-700/60 w-full">
            @foreach([
                ['text', 'document-text', 'Hash Generator'],
                ['file', 'arrow-up-tray', 'File Hash Generator'],
                ['compare', 'check-circle', 'Checksum Compare'],
            ] as [$key, $icon, $label])
            <button
                wire:click="$set('activeTab', '{{ $key }}')"
                @class([
                    'flex-1 flex items-center justify-center gap-1.5 px-3.5 py-2 rounded-xl text-sm font-semibold whitespace-nowrap min-w-[120px]',
                    'bg-white dark:bg-zinc-700 text-violet-600 dark:text-violet-400 shadow-sm font-bold' => $activeTab === $key,
                    'text-zinc-500 dark:text-zinc-400 hover:text-zinc-800 dark:hover:text-zinc-200' => $activeTab !== $key,
                ])>
                <flux:icon icon="{{ $icon }}" class="size-3.5" />
                {{ $label }}
            </button>
            @endforeach
        </div>
    </div>

    {{-- Workspace Grid --}}
    <div class="grid grid-cols-1 gap-8 items-start">

        {{-- Tab 1: Hash Generator (Text) --}}
        @if($activeTab === 'text')
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            <div class="lg:col-span-5">
                <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800/80 shadow-sm p-5 space-y-6">
                    <div class="flex items-center justify-between pb-3 border-b border-zinc-200 dark:border-zinc-800">
                        <div class="flex items-center gap-2">
                            <div class="p-1.5 rounded-lg bg-violet-50 dark:bg-violet-900/30">
                                <flux:icon icon="document-text" class="size-3.5 text-violet-600 dark:text-violet-400" />
                            </div>
                            <h2 class="text-xs font-bold text-zinc-700 dark:text-zinc-200 uppercase tracking-widest">Input Payload</h2>
                        </div>
                        <flux:button size="xs" variant="ghost" wire:click="clearText">Clear</flux:button>
                    </div>

                    <flux:textarea
                        label="Text Plaintext"
                        wire:model.live.debounce.300ms="textToHash"
                        placeholder="Type or paste the text to hash here..."
                        rows="8" />
                </div>
            </div>

            <div class="lg:col-span-7">
                <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800/80 shadow-sm p-5 space-y-6">
                    <div class="pb-3 border-b border-zinc-200 dark:border-zinc-800">
                        <div class="flex items-center gap-2">
                            <div class="p-1.5 rounded-lg bg-violet-50 dark:bg-violet-900/30">
                                <flux:icon icon="hashtag" class="size-3.5 text-violet-600 dark:text-violet-400" />
                            </div>
                            <h2 class="text-xs font-bold text-zinc-700 dark:text-zinc-200 uppercase tracking-widest">Generated Hashes</h2>
                        </div>
                    </div>

                    <div class="space-y-3">
                        @foreach($this->getHashes() as $algo => $hashVal)
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between p-3 rounded-xl bg-zinc-50 dark:bg-zinc-800/40 border border-zinc-200 dark:border-zinc-700/60 gap-3">
                            <span class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-widest sm:w-24 shrink-0">{{ $algo }}</span>
                            <span class="font-mono text-xs text-zinc-700 dark:text-zinc-300 break-all flex-1 select-all">
                                {{ $hashVal ?: 'Waiting for input...' }}
                            </span>
                            @if($hashVal)
                            <div x-data="{ copied: false }" class="shrink-0">
                                <flux:button
                                    size="xs"
                                    variant="ghost"
                                    class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 shadow-sm"
                                    x-on:click="
                                        if (navigator.clipboard && navigator.clipboard.writeText) {
                                            navigator.clipboard.writeText('{{ $hashVal }}').then(() => { copied = true; setTimeout(() => copied = false, 1800); });
                                        } else {
                                            const ta = document.createElement('textarea'); ta.value = '{{ $hashVal }}'; ta.style.position = 'fixed'; ta.style.opacity = '0'; document.body.appendChild(ta); ta.focus(); ta.select(); document.execCommand('copy'); document.body.removeChild(ta); copied = true; setTimeout(() => copied = false, 1800);
                                        }
                                    ">
                                    <flux:icon x-show="!copied" icon="clipboard" class="size-3" />
                                    <flux:icon x-show="copied" icon="check" class="size-3 text-green-500" />
                                    <span class="sr-only" x-text="copied ? 'Copied' : 'Copy'"></span>
                                </flux:button>
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Tab 2: File Hash Generator --}}
        @if($activeTab === 'file')
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            <div class="lg:col-span-5">
                <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800/80 shadow-sm p-5 space-y-6">
                    <div class="flex items-center justify-between pb-3 border-b border-zinc-200 dark:border-zinc-800">
                        <div class="flex items-center gap-2">
                            <div class="p-1.5 rounded-lg bg-violet-50 dark:bg-violet-900/30">
                                <flux:icon icon="arrow-up-tray" class="size-3.5 text-violet-600 dark:text-violet-400" />
                            </div>
                            <h2 class="text-xs font-bold text-zinc-700 dark:text-zinc-200 uppercase tracking-widest">File Upload</h2>
                        </div>
                        <flux:button size="xs" variant="ghost" wire:click="clearFile">Clear</flux:button>
                    </div>

                    {{-- Upload Area --}}
                    <div class="space-y-4">
                        <label class="flex flex-col items-center justify-center border-2 border-dashed border-zinc-300 dark:border-zinc-700 rounded-2xl p-8 hover:border-violet-500 hover:bg-zinc-50 dark:hover:bg-zinc-800/40 cursor-pointer transition group relative">
                            <flux:icon icon="arrow-up-tray" class="size-10 text-zinc-400 group-hover:text-violet-500 transition mb-3" />
                            <span class="text-sm font-semibold text-zinc-700 dark:text-zinc-300">Choose a file or drag it here</span>
                            <span class="text-xs text-zinc-400 dark:text-zinc-500 mt-1">Supports any file up to 50 MB</span>
                            <input type="file" wire:model="uploadedFile" class="hidden" />
                        </label>

                        @error('uploadedFile')
                        <div class="p-3 rounded-lg bg-red-50 dark:bg-red-950/20 border border-red-200 dark:border-red-900/50 text-xs text-red-600 dark:text-red-400">
                            {{ $message }}
                        </div>
                        @enderror

                        {{-- Upload Progress --}}
                        <div wire:loading wire:target="uploadedFile" class="p-4 rounded-xl bg-violet-50 dark:bg-violet-900/15 border border-violet-200 dark:border-violet-800/50 flex items-center gap-3 text-sm text-violet-700 dark:text-violet-400 w-full justify-center">
                            <flux:icon icon="loading" class="size-4 animate-spin text-violet-500" />
                            Uploading and processing file...
                        </div>

                        {{-- Metadata Details --}}
                        @if($fileMeta)
                        <div class="p-4 rounded-xl bg-zinc-50 dark:bg-zinc-800/30 border border-zinc-200 dark:border-zinc-800 space-y-2 text-xs">
                            <p class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider mb-1">File Info</p>
                            <div><span class="text-zinc-500 dark:text-zinc-400">Name:</span> <strong class="text-zinc-800 dark:text-zinc-200 font-mono break-all">{{ $fileMeta['name'] }}</strong></div>
                            <div><span class="text-zinc-500 dark:text-zinc-400">Size:</span> <strong class="text-zinc-800 dark:text-zinc-200">{{ $fileMeta['size'] }}</strong></div>
                            <div><span class="text-zinc-500 dark:text-zinc-400">Type:</span> <strong class="text-zinc-800 dark:text-zinc-200">{{ $fileMeta['mime'] }}</strong></div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="lg:col-span-7">
                <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800/80 shadow-sm p-5 space-y-6">
                    <div class="pb-3 border-b border-zinc-200 dark:border-zinc-800">
                        <div class="flex items-center gap-2">
                            <div class="p-1.5 rounded-lg bg-violet-50 dark:bg-violet-900/30">
                                <flux:icon icon="hashtag" class="size-3.5 text-violet-600 dark:text-violet-400" />
                            </div>
                            <h2 class="text-xs font-bold text-zinc-700 dark:text-zinc-200 uppercase tracking-widest">File Checksums</h2>
                        </div>
                    </div>

                    <div class="space-y-4">
                        @foreach([
                            'MD5' => $fileMd5,
                            'SHA-256' => $fileSha256,
                            'SHA-512' => $fileSha512
                        ] as $algo => $hashVal)
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-widest">{{ $algo }}</span>
                                @if($hashVal)
                                <div x-data="{ copied: false }">
                                    <button
                                        class="text-[10px] font-bold text-violet-600 dark:text-violet-400 uppercase tracking-wider hover:underline"
                                        x-on:click="
                                            if (navigator.clipboard && navigator.clipboard.writeText) {
                                                navigator.clipboard.writeText('{{ $hashVal }}').then(() => { copied = true; setTimeout(() => copied = false, 1800); });
                                            } else {
                                                const ta = document.createElement('textarea'); ta.value = '{{ $hashVal }}'; ta.style.position = 'fixed'; ta.style.opacity = '0'; document.body.appendChild(ta); ta.focus(); ta.select(); document.execCommand('copy'); document.body.removeChild(ta); copied = true; setTimeout(() => copied = false, 1800);
                                            }
                                        ">
                                        <span x-text="copied ? 'Copied!' : 'Copy Hash'"></span>
                                    </button>
                                </div>
                                @endif
                            </div>

                            <div class="p-3 rounded-xl bg-zinc-50 dark:bg-zinc-800/40 border border-zinc-200 dark:border-zinc-700/60 font-mono text-xs text-zinc-700 dark:text-zinc-300 break-all min-h-10 flex items-center">
                                {{ $hashVal ?: 'Select a file to compute checksum...' }}
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- Tab 3: Checksum Compare --}}
        @if($activeTab === 'compare')
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            <div class="lg:col-span-7">
                <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800/80 shadow-sm p-5 space-y-6">
                    <div class="flex items-center justify-between pb-3 border-b border-zinc-200 dark:border-zinc-800">
                        <div class="flex items-center gap-2">
                            <div class="p-1.5 rounded-lg bg-violet-50 dark:bg-violet-900/30">
                                <flux:icon icon="check-circle" class="size-3.5 text-violet-600 dark:text-violet-400" />
                            </div>
                            <h2 class="text-xs font-bold text-zinc-700 dark:text-zinc-200 uppercase tracking-widest">Compare Hash Payloads</h2>
                        </div>
                        <flux:button size="xs" variant="ghost" wire:click="clearCompare">Clear</flux:button>
                    </div>

                    <div class="space-y-4">
                        <flux:textarea
                            label="Hash A"
                            wire:model.live.debounce.300ms="hashA"
                            placeholder="Enter the first hash to compare..."
                            rows="4" />

                        <flux:textarea
                            label="Hash B"
                            wire:model.live.debounce.300ms="hashB"
                            placeholder="Enter the second hash to compare..."
                            rows="4" />
                    </div>
                </div>
            </div>

            <div class="lg:col-span-5">
                <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800/80 shadow-sm p-5 space-y-6">
                    <div class="pb-3 border-b border-zinc-200 dark:border-zinc-800">
                        <div class="flex items-center gap-2">
                            <div class="p-1.5 rounded-lg bg-violet-50 dark:bg-violet-900/30">
                                <flux:icon icon="hashtag" class="size-3.5 text-violet-600 dark:text-violet-400" />
                            </div>
                            <h2 class="text-xs font-bold text-zinc-700 dark:text-zinc-200 uppercase tracking-widest">Comparison Result</h2>
                        </div>
                    </div>

                    @if($compareResult === null)
                    <div class="flex flex-col items-center justify-center py-12 text-zinc-400 text-sm gap-2">
                        <flux:icon icon="information-circle" class="size-8 text-zinc-300 dark:text-zinc-600" />
                        <p>Provide both hashes to compare.</p>
                    </div>
                    @elseif($compareResult === true)
                    <div class="p-6 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-center space-y-3">
                        <div class="inline-flex p-3 rounded-full bg-emerald-500/20 text-emerald-500">
                            <flux:icon icon="check" class="size-8" />
                        </div>
                        <h3 class="text-lg font-bold text-emerald-700 dark:text-emerald-400">Checksums Match</h3>
                        <p class="text-xs text-emerald-600 dark:text-emerald-500 max-w-xs mx-auto leading-relaxed">
                            Both hash outputs are identical (case-insensitive and excluding leading/trailing spaces).
                        </p>
                    </div>
                    @else
                    <div class="p-6 rounded-2xl bg-red-500/10 border border-red-500/20 text-center space-y-3">
                        <div class="inline-flex p-3 rounded-full bg-red-500/20 text-red-500">
                            <flux:icon icon="x-mark" class="size-8" />
                        </div>
                        <h3 class="text-lg font-bold text-red-700 dark:text-red-400">Checksums Mismatch</h3>
                        <p class="text-xs text-red-600 dark:text-red-500 max-w-xs mx-auto leading-relaxed">
                            The hash outputs do not match. Check that they were generated using the same file/string and algorithm.
                        </p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endif

    </div>

</div>
