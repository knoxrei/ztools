<?php

use Livewire\Component;
use Livewire\Attributes\Title;
use phpseclib3\Crypt\AES;
use phpseclib3\Crypt\Blowfish;
use phpseclib3\Crypt\Twofish;
use phpseclib3\Crypt\DES;
use phpseclib3\Crypt\TripleDES;
use phpseclib3\Crypt\RC4;

new #[Title('Symmetric Encryption')] class extends Component
{
    public string $algo = 'aes-256-cbc';
    public string $input = '';
    public string $key = '';
    public string $iv = '';
    public bool $autoIv = true;
    public string $inputFormat = 'text'; // text / base64 / hex
    public string $outputFormat = 'base64'; // base64 / hex
    public string $output = '';
    public string $error = '';

    public function mount(): void
    {
        $this->process();
    }

    public function updated(): void
    {
        $this->process();
    }

    public function process(): void
    {
        $this->error = '';
        $this->output = '';

        if ($this->input === '') {
            return;
        }

        if ($this->key === '') {
            $this->error = 'Secret key is required.';
            return;
        }

        try {
            $rawData = $this->getRawInput();
            $keyBytes = $this->deriveKey($this->key, $this->algo);
            $ivLength = $this->getIvLength($this->algo);
            $ivBytes = '';

            if ($ivLength > 0) {
                if ($this->autoIv) {
                    $ivBytes = random_bytes($ivLength);
                    $this->iv = bin2hex($ivBytes);
                } else {
                    $ivBytes = $this->parseIv($this->iv, $ivLength);
                }
            }

            $ciphertext = $this->runEncrypt($this->algo, $rawData, $keyBytes, $ivBytes);

            if ($ivLength > 0) {
                $combined = $ivBytes . $ciphertext;
            } else {
                $combined = $ciphertext;
            }

            if ($this->outputFormat === 'hex') {
                $this->output = bin2hex($combined);
            } else {
                $this->output = base64_encode($combined);
            }
        } catch (Throwable $e) {
            $this->error = $e->getMessage();
        }
    }

    private function getRawInput(): string
    {
        if ($this->inputFormat === 'base64') {
            $dec = base64_decode($this->input, true);
            if ($dec === false) throw new Exception('Invalid base64 input.');
            return $dec;
        }
        if ($this->inputFormat === 'hex') {
            $cleaned = preg_replace('/[^0-9a-fA-F]/', '', $this->input);
            if (strlen($cleaned) % 2 !== 0) throw new Exception('Invalid hex string length.');
            $dec = hex2bin($cleaned);
            if ($dec === false) throw new Exception('Invalid hex encoding.');
            return $dec;
        }
        return $this->input;
    }

    private function parseIv(string $ivHex, int $length): string
    {
        $cleaned = preg_replace('/[^0-9a-fA-F]/', '', $ivHex);
        $bin = @hex2bin($cleaned);
        if ($bin === false || strlen($bin) !== $length) {
            $bin = substr(str_pad($cleaned, $length, "\0"), 0, $length);
        }
        return $bin;
    }

    private function deriveKey(string $key, string $algo): string
    {
        $hash = hash('sha256', $key, true);
        switch ($algo) {
            case 'aes-128-cbc':
                return substr($hash, 0, 16);
            case 'aes-192-cbc':
                return substr($hash, 0, 24);
            case 'aes-256-cbc':
            case 'chacha20':
            case 'twofish-cbc':
            case 'blowfish-cbc':
            case 'rc4':
                return $hash;
            case 'des-cbc':
                return substr($hash, 0, 8);
            case 'tripledes-cbc':
                return substr($hash, 0, 24);
            default:
                return $hash;
        }
    }

    private function getIvLength(string $algo): int
    {
        switch ($algo) {
            case 'aes-128-cbc':
            case 'aes-192-cbc':
            case 'aes-256-cbc':
            case 'twofish-cbc':
                return 16;
            case 'blowfish-cbc':
            case 'des-cbc':
            case 'tripledes-cbc':
                return 8;
            case 'chacha20':
                return openssl_cipher_iv_length('chacha20') ?: 16;
            case 'rc4':
                return 0;
            default:
                return 0;
        }
    }

    private function runEncrypt(string $algo, string $data, string $key, string $iv): string
    {
        switch ($algo) {
            case 'aes-128-cbc':
            case 'aes-192-cbc':
            case 'aes-256-cbc':
                $cipher = new AES('cbc');
                $cipher->setKey($key);
                $cipher->setIV($iv);
                return $cipher->encrypt($data);
            case 'twofish-cbc':
                $cipher = new Twofish('cbc');
                $cipher->setKey($key);
                $cipher->setIV($iv);
                return $cipher->encrypt($data);
            case 'blowfish-cbc':
                $cipher = new Blowfish('cbc');
                $cipher->setKey($key);
                $cipher->setIV($iv);
                return $cipher->encrypt($data);
            case 'des-cbc':
                $cipher = new DES('cbc');
                $cipher->setKey($key);
                $cipher->setIV($iv);
                return $cipher->encrypt($data);
            case 'tripledes-cbc':
                $cipher = new TripleDES('cbc');
                $cipher->setKey($key);
                $cipher->setIV($iv);
                return $cipher->encrypt($data);
            case 'rc4':
                $cipher = new RC4();
                $cipher->setKey($key);
                return $cipher->encrypt($data);
            case 'chacha20':
                $enc = openssl_encrypt($data, 'chacha20', $key, OPENSSL_RAW_DATA, $iv);
                if ($enc === false) throw new Exception('ChaCha20 encryption failed.');
                return $enc;
            default:
                throw new Exception('Unsupported encryption algorithm.');
        }
    }

    public function resetEnc(): void
    {
        $this->reset(['input', 'key', 'iv', 'output', 'error']);
        $this->autoIv = true;
    }
};
?>

<div class="min-h-screen pb-12">

    {{-- Page Header --}}
    <div class="mb-8">
        <div class="flex items-center gap-3 mb-2">
            <div class="p-2">
                <flux:icon icon="lock-closed" class="size-7 text-violet-600 dark:text-violet-400" />
            </div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white tracking-tight">Symmetric Encryption</h1>
        </div>
        <p class="text-sm text-zinc-500 dark:text-zinc-400 ml-14">
            Encrypt messages or payloads using AES, Blowfish, Twofish, ChaCha20, DES, Triple DES, or RC4.
        </p>
    </div>

    {{-- Workspace Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

        {{-- Inputs Panel --}}
        <div class="lg:col-span-7">
            <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800/80 shadow-sm p-5 space-y-6">
                <div class="flex items-center justify-between pb-3 border-b border-zinc-200 dark:border-zinc-800">
                    <div class="flex items-center gap-2">
                        <div class="p-1.5 rounded-lg bg-violet-50 dark:bg-violet-900/30">
                            <flux:icon icon="key" class="size-3.5 text-violet-600 dark:text-violet-400" />
                        </div>
                        <h2 class="text-xs font-bold text-zinc-700 dark:text-zinc-200 uppercase tracking-widest">Encryption Inputs</h2>
                    </div>
                    <flux:button size="xs" variant="ghost" wire:click="resetEnc">Clear</flux:button>
                </div>

                <div class="space-y-4">
                    {{-- Algorithms & Input Format --}}
                    <div class="grid grid-cols-2 gap-4">
                        <flux:select label="Algorithm" wire:model.live="algo">
                            <flux:select.option value="aes-256-cbc">AES-256 (CBC)</flux:select.option>
                            <flux:select.option value="aes-192-cbc">AES-192 (CBC)</flux:select.option>
                            <flux:select.option value="aes-128-cbc">AES-128 (CBC)</flux:select.option>
                            <flux:select.option value="chacha20">ChaCha20</flux:select.option>
                            <flux:select.option value="twofish-cbc">Twofish (CBC)</flux:select.option>
                            <flux:select.option value="blowfish-cbc">Blowfish (CBC)</flux:select.option>
                            <flux:select.option value="tripledes-cbc">Triple DES (3DES)</flux:select.option>
                            <flux:select.option value="des-cbc">DES (Single DES)</flux:select.option>
                            <flux:select.option value="rc4">RC4</flux:select.option>
                        </flux:select>

                        <flux:select label="Input Format" wire:model.live="inputFormat">
                            <flux:select.option value="text">Plain UTF-8 Text</flux:select.option>
                            <flux:select.option value="base64">Base64 Encoded</flux:select.option>
                            <flux:select.option value="hex">Hexadecimal Encoded</flux:select.option>
                        </flux:select>
                    </div>

                    {{-- Key & IV --}}
                    <div class="grid grid-cols-2 gap-4">
                        <flux:input
                            label="Passphrase / Key"
                            type="text"
                            wire:model.live.debounce.300ms="key"
                            placeholder="Secret key" />

                        @if($algo !== 'rc4')
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between">
                                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300">IV (Hex)</label>
                                <label class="flex items-center gap-1 cursor-pointer select-none">
                                    <input type="checkbox" wire:model.live="autoIv" class="rounded border-zinc-300 dark:border-zinc-700 text-zinc-800 focus:ring-zinc-400" />
                                    <span class="text-[9px] text-zinc-500 font-bold uppercase">Auto</span>
                                </label>
                            </div>
                            <flux:input
                                type="text"
                                wire:model.live.debounce.300ms="iv"
                                placeholder="IV hex string"
                                :disabled="$autoIv" />
                        </div>
                        @endif
                    </div>

                    {{-- Input Plaintext --}}
                    <flux:textarea
                        label="Plaintext Payload"
                        wire:model.live.debounce.400ms="input"
                        placeholder="Enter message to encrypt..."
                        rows="6" />
                </div>
            </div>
        </div>

        {{-- Outputs Panel --}}
        <div class="lg:col-span-5">
            <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800/80 shadow-sm p-5 space-y-6">
                <div class="flex items-center justify-between pb-3 border-b border-zinc-200 dark:border-zinc-800">
                    <div class="flex items-center gap-2">
                        <div class="p-1.5 rounded-lg bg-violet-50 dark:bg-violet-900/30">
                            <flux:icon icon="lock-closed" class="size-3.5 text-violet-600 dark:text-violet-400" />
                        </div>
                        <h2 class="text-xs font-bold text-zinc-700 dark:text-zinc-200 uppercase tracking-widest">Ciphertext Output</h2>
                    </div>
                    <div class="flex gap-2 text-xs select-none">
                        <button
                            wire:click="$set('outputFormat', 'base64')"
                            @class([
                                'font-bold uppercase tracking-wider text-[10px]',
                                'text-violet-600 dark:text-violet-400' => $outputFormat === 'base64',
                                'text-zinc-400 dark:text-zinc-500' => $outputFormat !== 'base64'
                            ])>Base64</button>
                        <span class="text-zinc-300 dark:text-zinc-700">|</span>
                        <button
                            wire:click="$set('outputFormat', 'hex')"
                            @class([
                                'font-bold uppercase tracking-wider text-[10px]',
                                'text-violet-600 dark:text-violet-400' => $outputFormat === 'hex',
                                'text-zinc-400 dark:text-zinc-500' => $outputFormat !== 'hex'
                            ])>Hex</button>
                    </div>
                </div>

                @if($error)
                <div class="p-3 rounded-lg bg-red-50 dark:bg-red-950/20 border border-red-200 dark:border-red-900/50 text-xs text-red-600 dark:text-red-400 font-mono break-all leading-normal">
                    {{ $error }}
                </div>
                @endif

                <div class="relative" x-data="{ copied: false }">
                    <flux:textarea readonly rows="10" x-text="$wire.output" placeholder="Encrypted ciphertext will appear here..." />
                    @if($output)
                    <div class="absolute bottom-2.5 right-2.5">
                        <flux:button
                            size="xs"
                            variant="ghost"
                            class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 shadow-sm"
                            x-on:click="
                                if (navigator.clipboard && navigator.clipboard.writeText) {
                                    navigator.clipboard.writeText($wire.output).then(() => { copied = true; setTimeout(() => copied = false, 1800); });
                                } else {
                                    const ta = document.createElement('textarea'); ta.value = $wire.output; ta.style.position = 'fixed'; ta.style.opacity = '0'; document.body.appendChild(ta); ta.focus(); ta.select(); document.execCommand('copy'); document.body.removeChild(ta); copied = true; setTimeout(() => copied = false, 1800);
                                }
                            ">
                            <flux:icon x-show="!copied" icon="clipboard" class="size-3" />
                            <flux:icon x-show="copied" icon="check" class="size-3 text-green-500" />
                            <span x-text="copied ? 'Copied' : 'Copy'"></span>
                        </flux:button>
                    </div>
                    @endif
                </div>
            </div>
        </div>

    </div>

</div>