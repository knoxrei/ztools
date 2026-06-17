<?php

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Title;

new #[Title('File Forensics')] class extends Component
{
    use WithFileUploads;

    public $uploadedFile;

    // ── Output Properties ──
    public array $fileMeta = [];
    public string $magicHex = '';
    public string $magicAscii = '';
    public ?array $matchedSig = null;
    public string $spoofStatus = ''; // matched, spoofed, unknown
    public array $exifData = [];
    public array $rawExif = [];
    public array $pdfData = [];

    private array $signatures = [
        'ffd8ff' => [
            'name' => 'JPEG / JPG Image',
            'exts' => ['jpg', 'jpeg'],
            'mime' => 'image/jpeg'
        ],
        '89504e47' => [
            'name' => 'PNG Image',
            'exts' => ['png'],
            'mime' => 'image/png'
        ],
        '25504446' => [
            'name' => 'PDF Document',
            'exts' => ['pdf'],
            'mime' => 'application/pdf'
        ],
        '504b0304' => [
            'name' => 'ZIP Archive / Office Open XML (DOCX, XLSX, PPTX)',
            'exts' => ['zip', 'docx', 'xlsx', 'pptx', 'jar', 'apk'],
            'mime' => 'application/zip'
        ],
        '52617221' => [
            'name' => 'RAR Archive',
            'exts' => ['rar'],
            'mime' => 'application/x-rar-compressed'
        ],
        '7f454c46' => [
            'name' => 'ELF Executable (Linux Binary)',
            'exts' => ['elf', 'bin', 'so'],
            'mime' => 'application/octet-stream'
        ],
        '4d5a' => [
            'name' => 'PE / EXE Executable (Windows Binary)',
            'exts' => ['exe', 'dll', 'sys'],
            'mime' => 'application/x-msdownload'
        ],
        '47494638' => [
            'name' => 'GIF Image',
            'exts' => ['gif'],
            'mime' => 'image/gif'
        ],
        '494433' => [
            'name' => 'MP3 Audio (with ID3 tag)',
            'exts' => ['mp3'],
            'mime' => 'audio/mpeg'
        ],
        '1a45dfa3' => [
            'name' => 'MKV / WebM Video Container',
            'exts' => ['mkv', 'webm'],
            'mime' => 'video/x-matroska'
        ],
        '52494646' => [ // RIFF
            'name' => 'RIFF Container (WAV / AVI / WEBP)',
            'exts' => ['wav', 'avi', 'webp'],
            'mime' => 'image/webp'
        ],
        '377abcaf271c' => [
            'name' => '7-Zip Archive',
            'exts' => ['7z'],
            'mime' => 'application/x-7z-compressed'
        ],
        '1f8b' => [
            'name' => 'GZIP Archive',
            'exts' => ['gz', 'tar.gz'],
            'mime' => 'application/gzip'
        ],
        '425a68' => [ // BZh
            'name' => 'BZIP2 Archive',
            'exts' => ['bz2'],
            'mime' => 'application/x-bzip2'
        ],
        '4f676753' => [
            'name' => 'Ogg Audio/Video Container',
            'exts' => ['ogg', 'ogv', 'oga'],
            'mime' => 'audio/ogg'
        ],
        '66747970' => [ // 'ftyp'
            'name' => 'MP4 / QuickTime Video',
            'exts' => ['mp4', 'mov', 'm4v'],
            'mime' => 'video/mp4'
        ],
        '3c21444f' => [ // <!DO
            'name' => 'HTML Document',
            'exts' => ['html', 'htm'],
            'mime' => 'text/html'
        ],
        '3c3f786d' => [ // <?xm
            'name' => 'XML Document',
            'exts' => ['xml'],
            'mime' => 'text/xml'
        ],
        '7b0a' => [ // {\n
            'name' => 'JSON Document',
            'exts' => ['json'],
            'mime' => 'application/json'
        ],
        '7b22' => [ // {"
            'name' => 'JSON Document',
            'exts' => ['json'],
            'mime' => 'application/json'
        ],
        '2321' => [ // #!
            'name' => 'Script File / Executable Script',
            'exts' => ['sh', 'py', 'pl', 'rb', 'php'],
            'mime' => 'text/plain'
        ]
    ];

    public function updatedUploadedFile(): void
    {
        $this->validate([
            'uploadedFile' => 'required|file|max:20480', // Limit to 20MB
        ]);

        try {
            $path = $this->uploadedFile->getRealPath();
            $originalName = $this->uploadedFile->getClientOriginalName();
            $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

            // 1. Read Magic Bytes (first 16 bytes)
            $fh = fopen($path, 'rb');
            if ($fh === false) {
                throw new Exception('Unable to open uploaded file.');
            }
            $magicBytes = fread($fh, 16);
            fclose($fh);

            $this->magicHex = strtoupper(bin2hex($magicBytes));
            
            // Build printable ASCII string
            $this->magicAscii = '';
            for ($i = 0; $i < strlen($magicBytes); $i++) {
                $ord = ord($magicBytes[$i]);
                if ($ord >= 32 && $ord <= 126) {
                    $this->magicAscii .= $magicBytes[$i];
                } else {
                    $this->magicAscii .= '.';
                }
            }

            // 2. Cross-reference signature
            $this->matchedSig = null;
            $this->spoofStatus = 'unknown';
            $hexLower = strtolower($this->magicHex);

            foreach ($this->signatures as $key => $info) {
                if (str_starts_with($hexLower, $key)) {
                    $this->matchedSig = $info;
                    break;
                }
            }

            if ($this->matchedSig) {
                if (in_array($ext, $this->matchedSig['exts'])) {
                    $this->spoofStatus = 'matched';
                } else {
                    $this->spoofStatus = 'spoofed';
                }
            } else {
                $this->spoofStatus = 'unknown';
            }

            // 3. Set file metadata
            $this->fileMeta = [
                'name' => $originalName,
                'size' => $this->formatSize($this->uploadedFile->getSize()),
                'mime' => $this->uploadedFile->getMimeType(),
                'ext' => $ext,
            ];

            // 4. Extract EXIF & Raw EXIF data (for JPEGs/TIFFs)
            $this->exifData = [];
            $this->rawExif = [];
            if (in_array($ext, ['jpg', 'jpeg', 'tif', 'tiff']) || ($this->matchedSig && count(array_intersect($this->matchedSig['exts'], ['jpg', 'jpeg'])) > 0)) {
                $this->exifData = $this->parseExif($path);
                
                // Read raw EXIF tags dump
                $raw = @exif_read_data($path);
                if ($raw !== false) {
                    $this->rawExif = $this->flattenArray($raw);
                }
            }

            // 5. Extract PDF Metadata (for PDFs)
            $this->pdfData = [];
            if ($ext === 'pdf' || ($this->matchedSig && in_array('pdf', $this->matchedSig['exts']))) {
                $this->pdfData = $this->parsePdfMetadata($path);
            }

        } catch (Throwable $e) {
            $this->addError('uploadedFile', 'Forensic parsing failed: ' . $e->getMessage());
        }
    }

    private function parseExif(string $path): array
    {
        try {
            $exif = @exif_read_data($path);
            if ($exif === false) {
                return [];
            }
            
            $gps = $this->getGps($exif);
            
            $formatted = [];
            if (!empty($exif['Make'])) $formatted['Make'] = trim($exif['Make']);
            if (!empty($exif['Model'])) $formatted['Model'] = trim($exif['Model']);
            if (!empty($exif['DateTimeOriginal'])) $formatted['DateTaken'] = $exif['DateTimeOriginal'];
            
            // Exposure settings
            if (!empty($exif['ExposureTime'])) $formatted['ExposureTime'] = $exif['ExposureTime'];
            if (!empty($exif['FNumber'])) $formatted['Aperture'] = 'f/' . $this->evalRatio($exif['FNumber']);
            
            if (!empty($exif['ISOSpeedRatings'])) {
                $formatted['ISO'] = is_array($exif['ISOSpeedRatings']) ? $exif['ISOSpeedRatings'][0] : $exif['ISOSpeedRatings'];
            }
            
            if (!empty($exif['FocalLength'])) $formatted['FocalLength'] = $this->evalRatio($exif['FocalLength']) . 'mm';
            
            // Lens metadata
            if (!empty($exif['LensInfo'])) $formatted['LensInfo'] = $exif['LensInfo'];
            if (!empty($exif['LensModel'])) $formatted['LensModel'] = trim($exif['LensModel']);
            
            if (!empty($exif['COMPUTED']['Width']) && !empty($exif['COMPUTED']['Height'])) {
                $formatted['Dimensions'] = $exif['COMPUTED']['Width'] . ' × ' . $exif['COMPUTED']['Height'] . ' px';
            }
            
            if (!empty($exif['Software'])) $formatted['Software'] = trim($exif['Software']);
            
            // GPS
            if ($gps) {
                $formatted['GPS'] = [
                    'lat' => $gps['lat'],
                    'lon' => $gps['lon'],
                    'alt' => $gps['alt'] ?? null,
                    'link' => sprintf('https://www.openstreetmap.org/?mlat=%f&mlon=%f&zoom=15', $gps['lat'], $gps['lon']),
                ];
            }
            
            return $formatted;
        } catch (Throwable $e) {
            return ['Error' => 'Failed parsing EXIF segments: ' . $e->getMessage()];
        }
    }

    private function evalRatio(string $ratio): float
    {
        $parts = explode('/', $ratio);
        if (count($parts) === 2 && $parts[1] > 0) {
            return (float) ($parts[0] / $parts[1]);
        }
        return (float) $ratio;
    }

    private function getGps(array $exif): ?array
    {
        if (!isset($exif['GPSLatitude'], $exif['GPSLatitudeRef'], $exif['GPSLongitude'], $exif['GPSLongitudeRef'])) {
            return null;
        }
        
        $lat = $this->getGpsCoord($exif['GPSLatitude'], $exif['GPSLatitudeRef']);
        $lon = $this->getGpsCoord($exif['GPSLongitude'], $exif['GPSLongitudeRef']);
        
        $alt = null;
        if (isset($exif['GPSAltitude'])) {
            $altVal = $this->evalRatio($exif['GPSAltitude']);
            $ref = isset($exif['GPSAltitudeRef']) ? ord($exif['GPSAltitudeRef']) : 0;
            if ($ref === 1) {
                $altVal = -$altVal;
            }
            $alt = $altVal;
        }
        
        return [
            'lat' => $lat,
            'lon' => $lon,
            'alt' => $alt
        ];
    }

    private function getGpsCoord(array $coord, string $ref): float
    {
        $degrees = count($coord) > 0 ? $this->evalRatio($coord[0]) : 0;
        $minutes = count($coord) > 1 ? $this->evalRatio($coord[1]) : 0;
        $seconds = count($coord) > 2 ? $this->evalRatio($coord[2]) : 0;
        
        $flip = ($ref === 'W' || $ref === 'S') ? -1 : 1;
        
        return $flip * ($degrees + ($minutes / 60) + ($seconds / 3600));
    }

    private function parsePdfMetadata(string $path): array
    {
        try {
            // Read first 1MB and last 512KB to extract Info trailer
            $size = filesize($path);
            $content = '';
            if ($size < 1572864) { // 1.5MB
                $content = file_get_contents($path);
            } else {
                $fh = fopen($path, 'rb');
                $content = fread($fh, 1048576); // First 1MB
                fseek($fh, -524288, SEEK_END);
                $content .= fread($fh, 524288); // Last 512KB
                fclose($fh);
            }

            $metadata = [
                'Title' => 'Not Specified',
                'Author' => 'Not Specified',
                'Subject' => 'Not Specified',
                'Keywords' => 'Not Specified',
                'Creator' => 'Not Specified',
                'Producer' => 'Not Specified',
                'CreationDate' => 'Not Specified',
                'ModDate' => 'Not Specified',
                'PageCount' => 'Not Specified',
            ];

            $keys = ['Title', 'Author', 'Creator', 'Producer', 'Subject', 'Keywords', 'CreationDate', 'ModDate'];
            foreach ($keys as $key) {
                if (preg_match('/\\/' . $key . '\\s*(\\(([^)]*)\\)|<([0-9a-fA-F]*)>)/i', $content, $matches)) {
                    if (!empty($matches[2])) {
                        $metadata[$key] = $this->cleanPdfString($matches[2]);
                    } elseif (!empty($matches[3])) {
                        $metadata[$key] = $this->hexToStr($matches[3]);
                    }
                }
            }

            if (preg_match_all('/\\/Type\\s*\\/Pages\\s*\\/Count\\s*([0-9]+)/i', $content, $matches)) {
                $metadata['PageCount'] = end($matches[1]);
            } elseif (preg_match_all('/\\/Count\\s*([0-9]+)\\s*\\/Type\\s*\\/Pages/i', $content, $matches)) {
                $metadata['PageCount'] = end($matches[1]);
            }

            return $metadata;
        } catch (Throwable $e) {
            return ['Error' => 'Failed parsing PDF streams: ' . $e->getMessage()];
        }
    }

    private function cleanPdfString(string $str): string
    {
        if (str_starts_with($str, 'D:')) {
            $dateStr = substr($str, 2);
            if (strlen($dateStr) >= 8) {
                $year = substr($dateStr, 0, 4);
                $month = substr($dateStr, 4, 2);
                $day = substr($dateStr, 6, 2);
                $hour = strlen($dateStr) >= 10 ? substr($dateStr, 8, 2) : '00';
                $min = strlen($dateStr) >= 12 ? substr($dateStr, 10, 2) : '00';
                $sec = strlen($dateStr) >= 14 ? substr($dateStr, 12, 2) : '00';
                return "$year-$month-$day $hour:$min:$sec";
            }
        }
        $str = stripcslashes($str);
        $str = str_replace("\0", '', $str);
        return trim($str);
    }

    private function hexToStr(string $hex): string
    {
        $str = '';
        if (str_starts_with(strtolower($hex), 'feff')) {
            $hex = substr($hex, 4);
            for ($i = 0; $i < strlen($hex) - 1; $i += 4) {
                $charHex = substr($hex, $i, 4);
                $str .= mb_convert_encoding(pack('H*', $charHex), 'UTF-8', 'UTF-16BE');
            }
            return $str;
        }
        for ($i = 0; $i < strlen($hex) - 1; $i += 2) {
            $str .= chr(hexdec($hex[$i] . $hex[$i + 1]));
        }
        return trim($str);
    }

    private function flattenArray(array $array, string $prefix = ''): array
    {
        $result = [];
        foreach ($array as $key => $value) {
            $newKey = $prefix === '' ? $key : $prefix . '.' . $key;
            if (is_array($value)) {
                $result = array_merge($result, $this->flattenArray($value, $newKey));
            } else {
                if (is_resource($value)) {
                    $result[$newKey] = '[Resource]';
                } elseif (is_object($value)) {
                    $result[$newKey] = '[Object]';
                } else {
                    if (is_string($value)) {
                        $cleaned = mb_convert_encoding($value, 'UTF-8', 'UTF-8,ISO-8859-1,ASCII');
                        $result[$newKey] = trim($cleaned);
                    } else {
                        $result[$newKey] = (string) $value;
                    }
                }
            }
        }
        return $result;
    }

    public function getPreviewUrl(): ?string
    {
        if (!$this->uploadedFile) return null;
        $ext = strtolower(pathinfo($this->uploadedFile->getClientOriginalName(), PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
            return null;
        }
        try {
            return $this->uploadedFile->temporaryUrl();
        } catch (Throwable $e) {
            return null;
        }
    }

    public function clear(): void
    {
        $this->reset(['uploadedFile', 'fileMeta', 'magicHex', 'magicAscii', 'matchedSig', 'spoofStatus', 'exifData', 'rawExif', 'pdfData']);
        $this->resetErrorBag('uploadedFile');
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
                <flux:icon icon="eye" class="size-7 text-violet-600 dark:text-violet-400" />
            </div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white tracking-tight">Advanced Image & File Forensics</h1>
        </div>
        <p class="text-sm text-zinc-500 dark:text-zinc-400 ml-14">
            Verify magic bytes signatures, detect file extension spoofing, and run advanced EXIF/ExifTool diagnostic analyses.
        </p>
    </div>

    {{-- Workspace Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

        {{-- Left: Upload & File Preview & Magic Bytes --}}
        <div class="lg:col-span-5 space-y-6">
            <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800/80 shadow-sm p-5 space-y-6">
                <div class="flex items-center justify-between pb-3 border-b border-zinc-200 dark:border-zinc-800">
                    <div class="flex items-center gap-2">
                        <div class="p-1.5 rounded-lg bg-violet-50 dark:bg-violet-900/30">
                            <flux:icon icon="eye" class="size-3.5 text-violet-600 dark:text-violet-400" />
                        </div>
                        <h2 class="text-xs font-bold text-zinc-700 dark:text-zinc-200 uppercase tracking-widest">File Analyzer & Preview</h2>
                    </div>
                    @if($uploadedFile)
                    <flux:button size="xs" variant="ghost" wire:click="clear">Clear</flux:button>
                    @endif
                </div>

                {{-- Upload Area --}}
                <div class="space-y-4">
                    <label class="flex flex-col items-center justify-center border-2 border-dashed border-zinc-300 dark:border-zinc-700 rounded-2xl p-8 hover:border-violet-500 hover:bg-zinc-50 dark:hover:bg-zinc-800/40 cursor-pointer transition group relative">
                        <flux:icon icon="arrow-up-tray" class="size-10 text-zinc-400 group-hover:text-violet-500 transition mb-3" />
                        <span class="text-sm font-semibold text-zinc-700 dark:text-zinc-300">Choose a file or drag it here</span>
                        <span class="text-xs text-zinc-400 dark:text-zinc-500 mt-1">Supports images, PDFs, archives, executables (Up to 20 MB)</span>
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
                        Uploading and parsing file...
                    </div>
                </div>

                @if($uploadedFile)
                <div class="space-y-6 pt-4 border-t border-zinc-100 dark:border-zinc-800/80">
                    {{-- Visual Preview --}}
                    <div>
                        <span class="block text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-widest mb-3">Visual Preview</span>
                        @if($this->getPreviewUrl())
                        <div class="flex justify-center bg-zinc-50 dark:bg-zinc-900/60 p-4 rounded-2xl border border-zinc-200 dark:border-zinc-800">
                            <img src="{{ $this->getPreviewUrl() }}" class="max-h-64 rounded-xl object-contain shadow-sm" alt="Uploaded Preview" />
                        </div>
                        @else
                        <div class="flex flex-col items-center justify-center py-12 text-zinc-400 dark:text-zinc-500 text-sm gap-2 text-center bg-zinc-50 dark:bg-zinc-900/60 rounded-2xl border border-zinc-200 dark:border-zinc-800">
                            <flux:icon icon="document" class="size-12 text-zinc-300 dark:text-zinc-700" />
                            <p class="font-semibold text-zinc-700 dark:text-zinc-300 break-all px-4">{{ $fileMeta['name'] ?? 'Uploaded File' }}</p>
                            <p class="text-xs text-zinc-500">Preview not available for this format.</p>
                        </div>
                        @endif
                    </div>

                    {{-- File Basic Metadata --}}
                    @if($fileMeta)
                    <div class="space-y-3 pt-4 border-t border-zinc-100 dark:border-zinc-800/80">
                        <span class="block text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-widest">General Metadata</span>
                        
                        <div class="space-y-2 text-xs">
                            <div class="flex justify-between py-1 border-b border-zinc-50 dark:border-zinc-800/50">
                                <span class="text-zinc-500">Filename:</span>
                                <span class="font-mono text-zinc-800 dark:text-zinc-200 font-semibold break-all text-right max-w-[200px]">{{ $fileMeta['name'] }}</span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-zinc-50 dark:border-zinc-800/50">
                                <span class="text-zinc-500">MIME Type:</span>
                                <span class="text-zinc-800 dark:text-zinc-200 font-semibold">{{ $fileMeta['mime'] }}</span>
                            </div>
                            <div class="flex justify-between py-1 border-b border-zinc-50 dark:border-zinc-800/50">
                                <span class="text-zinc-500">File Size:</span>
                                <span class="text-zinc-800 dark:text-zinc-200 font-semibold">{{ $fileMeta['size'] }}</span>
                            </div>
                            <div class="flex justify-between py-1">
                                <span class="text-zinc-500">Extension:</span>
                                <span class="font-mono text-zinc-800 dark:text-zinc-200 font-semibold">.{{ $fileMeta['ext'] }}</span>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Magic Bytes signature card --}}
                    @if($magicHex)
                    <div class="space-y-4 pt-4 border-t border-zinc-100 dark:border-zinc-800/80">
                        <span class="block text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-widest">Magic Bytes & Signatures</span>

                        {{-- Raw bytes display --}}
                        <div class="space-y-2">
                            <span class="block text-[10px] font-bold text-zinc-400 uppercase">First 16 Bytes (Hex)</span>
                            <div class="p-3 bg-zinc-900 text-zinc-100 rounded-xl font-mono text-[11px] leading-relaxed break-all tracking-wider select-all">
                                {{ chunk_split($magicHex, 2, ' ') }}
                            </div>
                        </div>

                        <div class="space-y-2">
                            <span class="block text-[10px] font-bold text-zinc-400 uppercase">ASCII Representation</span>
                            <div class="p-3 bg-zinc-900 text-zinc-300 rounded-xl font-mono text-[11px] leading-relaxed break-all select-all">
                                {{ $magicAscii }}
                            </div>
                        </div>

                        {{-- Signature Analysis --}}
                        <div class="pt-2">
                            @if($spoofStatus === 'matched')
                            <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-start gap-3">
                                <flux:icon icon="check-circle" class="size-5 text-emerald-500 shrink-0 mt-0.5" />
                                <div class="text-xs space-y-1">
                                    <h4 class="font-bold text-emerald-800 dark:text-emerald-400">Signature Valid</h4>
                                    <p class="text-emerald-700 dark:text-emerald-500/80 leading-normal">
                                        The file signature matches the extension (.{{ $fileMeta['ext'] }}). Identified as <strong>{{ $matchedSig['name'] }}</strong>.
                                    </p>
                                </div>
                            </div>
                            @elseif($spoofStatus === 'spoofed')
                            <div class="p-4 rounded-xl bg-red-500/10 border border-red-500/20 flex items-start gap-3">
                                <flux:icon icon="exclamation-triangle" class="size-5 text-red-500 shrink-0 mt-0.5" />
                                <div class="text-xs space-y-1">
                                    <h4 class="font-bold text-red-800 dark:text-red-400">Spoofing Warning!</h4>
                                    <p class="text-red-700 dark:text-red-500/80 leading-normal">
                                        The file extension (.{{ $fileMeta['ext'] }}) does not match its signature content. The file signature matches <strong>{{ $matchedSig['name'] }}</strong>.
                                    </p>
                                </div>
                            </div>
                            @else
                            <div class="p-4 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-start gap-3">
                                <flux:icon icon="information-circle" class="size-5 text-amber-500 shrink-0 mt-0.5" />
                                <div class="text-xs space-y-1">
                                    <h4 class="font-bold text-amber-800 dark:text-amber-400">Unknown Signature</h4>
                                    <p class="text-amber-700 dark:text-amber-500/80 leading-normal">
                                        The file's magic bytes do not match any standard signature database rules. Verification skipped.
                                    </p>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
                @endif
            </div>
        </div>

        {{-- Right: Content Metadata Extraction (PDF, EXIF Summary, GPS, Raw Exif as separate sections) --}}
        <div class="lg:col-span-7 space-y-6">

            {{-- Default blank state --}}
            @if(!$uploadedFile)
            <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800/80 shadow-sm p-5 flex flex-col items-center justify-center py-32 text-zinc-400 dark:text-zinc-500 text-sm gap-3">
                <flux:icon icon="document" class="size-12 text-zinc-300 dark:text-zinc-700" />
                <p>Upload a PDF or image file to view embedded metadata.</p>
            </div>
            @endif

            {{-- Section 1: PDF Metadata --}}
            @if($pdfData)
            <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800/80 shadow-sm p-5 space-y-6">
                <div class="pb-3 border-b border-zinc-200 dark:border-zinc-800">
                    <div class="flex items-center gap-2">
                        <div class="p-1.5 rounded-lg bg-violet-50 dark:bg-violet-900/30">
                            <flux:icon icon="document-text" class="size-3.5 text-violet-600 dark:text-violet-400" />
                        </div>
                        <h2 class="text-xs font-bold text-zinc-700 dark:text-zinc-200 uppercase tracking-widest">PDF Metadata Summary</h2>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($pdfData as $key => $val)
                    @if($key !== 'Error')
                    <div class="p-3 bg-zinc-50 dark:bg-zinc-800/40 border border-zinc-200 dark:border-zinc-800 rounded-xl space-y-1">
                        <span class="block text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-wide">{{ preg_replace('/(?<!^)(?=[A-Z])/', ' ', $key) }}</span>
                        <span class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 break-all select-all">
                            {{ $val }}
                        </span>
                    </div>
                    @else
                    <div class="col-span-2 p-3 bg-red-500/10 border border-red-500/20 text-xs text-red-600 dark:text-red-400 rounded-xl">
                        {{ $val }}
                    </div>
                    @endif
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Section 2: Camera & EXIF Summary --}}
            @if($exifData)
            <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800/80 shadow-sm p-5 space-y-6">
                <div class="pb-3 border-b border-zinc-200 dark:border-zinc-800">
                    <div class="flex items-center gap-2">
                        <div class="p-1.5 rounded-lg bg-violet-50 dark:bg-violet-900/30">
                            <flux:icon icon="camera" class="size-3.5 text-violet-600 dark:text-violet-400" />
                        </div>
                        <h2 class="text-xs font-bold text-zinc-700 dark:text-zinc-200 uppercase tracking-widest">Summary & Camera</h2>
                    </div>
                </div>

                {{-- Exposure settings badges --}}
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <div class="p-3 bg-zinc-50 dark:bg-zinc-800/40 border border-zinc-200 dark:border-zinc-800 rounded-2xl text-center space-y-1">
                        <span class="block text-[9px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-widest">Aperture</span>
                        <span class="block text-sm font-bold text-zinc-800 dark:text-zinc-100 font-mono">{{ $exifData['Aperture'] ?? '—' }}</span>
                    </div>
                    <div class="p-3 bg-zinc-50 dark:bg-zinc-800/40 border border-zinc-200 dark:border-zinc-800 rounded-2xl text-center space-y-1">
                        <span class="block text-[9px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-widest">Shutter</span>
                        <span class="block text-sm font-bold text-zinc-800 dark:text-zinc-100 font-mono">{{ $exifData['ExposureTime'] ?? '—' }}</span>
                    </div>
                    <div class="p-3 bg-zinc-50 dark:bg-zinc-800/40 border border-zinc-200 dark:border-zinc-800 rounded-2xl text-center space-y-1">
                        <span class="block text-[9px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-widest">ISO Speed</span>
                        <span class="block text-sm font-bold text-zinc-800 dark:text-zinc-100 font-mono">ISO {{ $exifData['ISO'] ?? '—' }}</span>
                    </div>
                    <div class="p-3 bg-zinc-50 dark:bg-zinc-800/40 border border-zinc-200 dark:border-zinc-800 rounded-2xl text-center space-y-1">
                        <span class="block text-[9px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-widest">Focal Length</span>
                        <span class="block text-sm font-bold text-zinc-800 dark:text-zinc-100 font-mono">{{ $exifData['FocalLength'] ?? '—' }}</span>
                    </div>
                </div>

                {{-- Details list --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="p-3.5 bg-zinc-50 dark:bg-zinc-800/20 border border-zinc-200/60 dark:border-zinc-800 rounded-xl space-y-1">
                        <span class="block text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase">Camera Make / Model</span>
                        <span class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300">
                            {{ isset($exifData['Make']) ? $exifData['Make'] . ' ' . ($exifData['Model'] ?? '') : 'Not Specified' }}
                        </span>
                    </div>
                    <div class="p-3.5 bg-zinc-50 dark:bg-zinc-800/20 border border-zinc-200/60 dark:border-zinc-800 rounded-xl space-y-1">
                        <span class="block text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase">Lens Specifications</span>
                        <span class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300">
                            {{ $exifData['LensModel'] ?? 'Not Specified' }}
                        </span>
                    </div>
                    <div class="p-3.5 bg-zinc-50 dark:bg-zinc-800/20 border border-zinc-200/60 dark:border-zinc-800 rounded-xl space-y-1">
                        <span class="block text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase">Software / Creator Editor</span>
                        <span class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 font-mono">
                            {{ $exifData['Software'] ?? 'Not Specified' }}
                        </span>
                    </div>
                    <div class="p-3.5 bg-zinc-50 dark:bg-zinc-800/20 border border-zinc-200/60 dark:border-zinc-800 rounded-xl space-y-1">
                        <span class="block text-[10px] font-bold text-zinc-400 dark:text-zinc-500 uppercase">Original Capture Time</span>
                        <span class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300">
                            {{ $exifData['DateTaken'] ?? 'Not Specified' }}
                        </span>
                    </div>
                </div>
            </div>
            @endif

            {{-- Section 3: GPS Coordinates --}}
            @if(isset($exifData['GPS']))
            <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800/80 shadow-sm p-5 space-y-4">
                <div class="pb-3 border-b border-zinc-200 dark:border-zinc-800">
                    <div class="flex items-center gap-2">
                        <div class="p-1.5 rounded-lg bg-violet-50 dark:bg-violet-900/30">
                            <flux:icon icon="map" class="size-3.5 text-violet-600 dark:text-violet-400" />
                        </div>
                        <h2 class="text-xs font-bold text-zinc-700 dark:text-zinc-200 uppercase tracking-widest">GPS Coordinates</h2>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs font-mono">
                    <div class="space-y-1">
                        <span class="block text-[9px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-widest">Latitude</span>
                        <span class="text-zinc-800 dark:text-zinc-200 font-semibold break-all">{{ $exifData['GPS']['lat'] }}</span>
                    </div>
                    <div class="space-y-1">
                        <span class="block text-[9px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-widest">Longitude</span>
                        <span class="text-zinc-800 dark:text-zinc-200 font-semibold break-all">{{ $exifData['GPS']['lon'] }}</span>
                    </div>
                    <div class="space-y-1">
                        <span class="block text-[9px] font-bold text-zinc-400 dark:text-zinc-500 uppercase tracking-widest">Altitude (MSL)</span>
                        <span class="text-zinc-800 dark:text-zinc-200 font-semibold">
                            {{ $exifData['GPS']['alt'] !== null ? $exifData['GPS']['alt'] . ' meters' : 'Not Recorded' }}
                        </span>
                    </div>
                </div>

                <div class="pt-3 border-t border-zinc-200 dark:border-zinc-800 flex justify-end">
                    <a href="{{ $exifData['GPS']['link'] }}" target="_blank" class="flex items-center gap-2 px-4 py-2 bg-violet-600 hover:bg-violet-700 text-white text-xs font-bold rounded-xl shadow transition">
                        <flux:icon icon="map-pin" class="size-3.5" />
                        Explore location on OpenStreetMap
                    </a>
                </div>
            </div>
            @endif

            {{-- Section 4: ExifTool Raw Dump --}}
            @if($rawExif)
            <div x-data="{ searchQuery: '' }" class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800/80 shadow-sm p-5 space-y-4">
                <div class="pb-3 border-b border-zinc-200 dark:border-zinc-800">
                    <div class="flex items-center gap-2">
                        <div class="p-1.5 rounded-lg bg-violet-50 dark:bg-violet-900/30">
                            <flux:icon icon="document-text" class="size-3.5 text-violet-600 dark:text-violet-400 font-mono" />
                        </div>
                        <h2 class="text-xs font-bold text-zinc-700 dark:text-zinc-200 uppercase tracking-widest font-mono">ExifTool Raw Dump</h2>
                    </div>
                </div>

                <div class="space-y-3">
                    <flux:input
                        x-model="searchQuery"
                        placeholder="Filter raw tags (e.g. Model, ISO, DateTime)..."
                        class="w-full text-xs font-mono"
                        icon="magnifying-glass" />
                    
                    <div class="max-h-80 overflow-y-auto border border-zinc-200 dark:border-zinc-800 rounded-xl divide-y divide-zinc-100 dark:divide-zinc-800 bg-zinc-50 dark:bg-zinc-900/60 font-mono text-[11px] leading-normal">
                        @foreach($rawExif as $key => $val)
                        <div
                            x-show="searchQuery === '' || '{{ strtolower($key) }}'.includes(searchQuery.toLowerCase()) || '{{ strtolower(addslashes($val)) }}'.includes(searchQuery.toLowerCase())"
                            class="flex flex-col sm:flex-row sm:items-center justify-between p-2.5 gap-2 hover:bg-zinc-100 dark:hover:bg-zinc-800/40">
                            <span class="text-zinc-500 dark:text-zinc-400 font-semibold break-all sm:w-1/3 shrink-0">{{ $key }}</span>
                            <span class="text-zinc-800 dark:text-zinc-200 break-all select-all flex-1">{{ $val }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            {{-- Generic other files upload (neither EXIF nor PDF parsed) --}}
            @if($uploadedFile && empty($exifData) && empty($pdfData))
            <div class="bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800/80 shadow-sm p-5 flex flex-col items-center justify-center py-20 text-zinc-400 dark:text-zinc-500 text-sm gap-2 text-center max-w-sm mx-auto">
                <flux:icon icon="information-circle" class="size-10 text-zinc-300 dark:text-zinc-700" />
                <h3 class="font-semibold text-zinc-700 dark:text-zinc-300">No segment parsers matched</h3>
                <p class="text-xs text-zinc-500 dark:text-zinc-500 leading-relaxed">
                    This file format signature does not contain standard EXIF segments or PDF Info object patterns. Basic file statistics are displayed in the signature panel.
                </p>
            </div>
            @endif
        </div>

    </div>

</div>
