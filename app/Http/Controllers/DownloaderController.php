<?php

namespace App\Http\Controllers;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;

class DownloaderController extends Controller
{
    /**
     * Proxy a POST request to the Downloadgram API and return parsed download items.
     */
    public function fetch(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'url' => ['required', 'string', 'max:2048'],
            'platform' => ['nullable', 'string', 'in:instagram,tiktok'],
        ]);

        $url = $validated['url'];
        $platform = $request->input('platform', '');

        // Automatically route to TikTok if specified or if the URL contains tiktok.com
        if ($platform === 'tiktok' || str_contains($url, 'tiktok.com')) {
            return $this->fetchTiktok($request);
        }

        $apiUrl = 'https://api.downloadgram.org/media';

        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (compatible; ZKnox/1.0)',
                    'Accept' => 'text/html,application/xhtml+xml',
                    'Referer' => 'https://downloadgram.org/',
                ])
                ->asForm()
                ->post($apiUrl, [
                    'url' => $url,
                    'submit' => '',
                ]);

            if (! $response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'The download service returned an error. Check the URL and try again.',
                ], 502);
            }

            $html = $response->body();

            // Parse all download items from #downloadhere .row sections
            $items = $this->parseDownloadItems($html);

            if (empty($items)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No downloadable media found. The URL may be private, expired, or unsupported.',
                ]);
            }

            return response()->json([
                'success' => true,
                'items' => $items,
            ]);
        } catch (ConnectionException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Could not connect to the download service. Try again later.',
            ], 503);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred.',
            ], 500);
        }
    }

    /**
     * Proxy a thumbnail image server-side to bypass CORS/hotlink protection.
     */
    public function thumb(Request $request): Response|JsonResponse
    {
        $url = $request->query('url', '');

        if (empty($url) || ! filter_var($url, FILTER_VALIDATE_URL)) {
            return response('Bad Request', 400);
        }

        // Only allow known CDN domains
        $allowedHosts = [
            'scontent', 'cdninstagram.com', 'instagram.com',
            'cdn.downloadgram.org', 'fbcdn.net',
            'ssstik', 'ssscdn.io', 'tiktokcdn.com', 'tiktok.com', 'byteoversea.com', 'ibyteimg.com', 'tikcdn.io'
        ];

        $host = parse_url($url, PHP_URL_HOST) ?? '';
        $allowed = false;
        foreach ($allowedHosts as $pattern) {
            if (str_contains($host, $pattern)) {
                $allowed = true;
                break;
            }
        }

        if (! $allowed) {
            return response('Forbidden', 403);
        }

        $referer = 'https://www.instagram.com/';
        if (str_contains($url, 'tiktok') || str_contains($url, 'byte') || str_contains($url, 'ssstik') || str_contains($url, 'ssscdn')) {
            $referer = 'https://www.tiktok.com/';
        }

        try {
            $img = Http::timeout(15)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                    'Referer' => $referer,
                    'Accept' => 'image/webp,image/apng,image/*,*/*;q=0.8',
                ])
                ->get($url);

            if (! $img->successful()) {
                return response('Not Found', 404);
            }

            $contentType = $img->header('Content-Type') ?: 'image/jpeg';

            return response($img->body(), 200, [
                'Content-Type' => $contentType,
                'Cache-Control' => 'public, max-age=3600',
                'X-Frame-Options' => 'SAMEORIGIN',
            ]);
        } catch (\Throwable $e) {
            return response('Error', 502);
        }
    }

    /**
     * Proxy a file download and stream it directly to bypass inline browser play and CORS.
     */
    public function download(Request $request): Response|JsonResponse
    {
        $url = $request->query('url', '');
        $filename = $request->query('filename', '');

        if (empty($url) || ! filter_var($url, FILTER_VALIDATE_URL)) {
            return response('Bad Request', 400);
        }

        if (empty($filename)) {
            $filename = basename(parse_url($url, PHP_URL_PATH)) ?: 'download';
        }

        // Only allow known CDN domains
        $allowedHosts = [
            'scontent', 'cdninstagram.com', 'instagram.com',
            'cdn.downloadgram.org', 'fbcdn.net',
            'ssstik', 'ssscdn.io', 'tiktokcdn.com', 'tiktok.com', 'byteoversea.com', 'ibyteimg.com', 'tikcdn.io'
        ];

        $host = parse_url($url, PHP_URL_HOST) ?? '';
        $allowed = false;
        foreach ($allowedHosts as $pattern) {
            if (str_contains($host, $pattern)) {
                $allowed = true;
                break;
            }
        }

        if (! $allowed) {
            return response('Forbidden', 403);
        }

        // Sanitize filename
        $filename = preg_replace('/[^a-zA-Z0-9_.-]/', '_', $filename);
        $filename = preg_replace('/__+/', '_', $filename);
        $filename = trim($filename, '_');

        $referer = 'https://www.instagram.com/';
        if (str_contains($url, 'tiktok') || str_contains($url, 'byte') || str_contains($url, 'ssstik') || str_contains($url, 'ssscdn')) {
            $referer = 'https://www.tiktok.com/';
        }

        try {
            $response = Http::timeout(60)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                    'Referer' => $referer,
                ])
                ->get($url);

            if (! $response->successful()) {
                return response('Failed to retrieve file from source', 502);
            }

            $contentType = $response->header('Content-Type') ?: 'application/octet-stream';

            return response($response->body(), 200, [
                'Content-Type' => $contentType,
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Cache-Control' => 'no-cache, private',
            ]);
        } catch (\Throwable $e) {
            return response('Error downloading file', 500);
        }
    }

    /**
     * Parse download items (thumbnail + download link) from Downloadgram HTML response.
     *
     * @return array<int, array{thumb: string|null, url: string, filename: string}>
     */
    private function parseDownloadItems(string $responseBody): array
    {
        $items = [];

        // The API returns a JavaScript string like:
        // document['getElementById']('div_download')['innerHTML']='<section\x20class=\x22...';
        // We need to decode the hex escapes (\x22 -> ", \x20 -> space, etc)
        $decodedBody = preg_replace_callback('/\\\\x([0-9A-Fa-f]{2})/', function ($m) {
            return chr(hexdec($m[1]));
        }, $responseBody);

        // Also decode \/ to /
        $decodedBody = str_replace('\/', '/', $decodedBody);

        // Try to extract the HTML part from the JS assignment
        $htmlToParse = $decodedBody;
        if (preg_match("/\['innerHTML'\]\s*=\s*'(.*?)'/s", $decodedBody, $matches)) {
            $htmlToParse = $matches[1];
        }

        // Suppress libxml errors for invalid HTML
        $dom = new \DOMDocument;
        @$dom->loadHTML('<?xml encoding="utf-8" ?><div>'.$htmlToParse.'</div>');
        $xpath = new \DOMXPath($dom);

        // Find all anchor tags that might be downloads
        $links = $xpath->query('//a[@href]');

        foreach ($links as $a) {
            $url = $a->getAttribute('href');

            // Skip invalid or irrelevant links like "/" or non-CDN links if they exist
            if (empty($url) || $url === '/' || $url === 'https://downloadgram.org/') {
                continue;
            }

            // Derive a clean filename from the CDN token if possible (do before resolving URL)
            $filename = 'download';
            if (strpos($url, 'token=') !== false) {
                $tokenPart = explode('token=', $url)[1] ?? '';
                $parts = explode('.', $tokenPart);
                $payload = isset($parts[1]) ? base64_decode(strtr($parts[1], '-_', '+/')) : '';
                if ($payload && preg_match('/"filename"\s*:\s*"([^"]+)"/', $payload, $m)) {
                    $filename = $m[1];
                } else {
                    $filename = basename(parse_url($url, PHP_URL_PATH)) ?: 'download';
                }
            } else {
                $filename = basename(parse_url($url, PHP_URL_PATH)) ?: 'download';
            }

            $item = [
                'thumb' => null,
                'url' => $this->resolveRealUrl($url),
                'filename' => $filename,
                'label' => trim($a->textContent) ?: 'DOWNLOAD',
            ];

            // Try to find a related image by going up the DOM tree and finding the first img
            $parent = $a->parentNode;
            while ($parent && $parent->nodeName !== 'div' && $parent->nodeName !== 'section' && $parent->nodeName !== 'body') {
                $parent = $parent->parentNode;
            }

            $thumb = null;
            if ($parent) {
                $imgs = $xpath->query('.//img[@src]', $parent);
                if ($imgs->length > 0) {
                    $thumb = $imgs->item(0)->getAttribute('src');
                }
            }

            // Fallback: Just get the first image in the whole document
            if (! $thumb) {
                $allImgs = $xpath->query('//img[@src]');
                if ($allImgs->length > 0) {
                    $thumb = $allImgs->item(0)->getAttribute('src');
                }
            }

            if ($thumb) {
                $item['thumb'] = $this->resolveRealUrl($thumb);
            }

            $items[] = $item;
        }

        // Remove duplicates by URL
        $unique = [];
        foreach ($items as $item) {
            $unique[$item['url']] = $item;
        }

        return array_values($unique);
    }

    /**
     * Decode JWT token parameter from Downloadgram CDN URL to resolve the direct media source URL.
     * This bypasses client-side ad blockers blocking downloadgram.org CDN subdomains.
     */
    private function resolveRealUrl(string $url): string
    {
        if (strpos($url, 'token=') !== false) {
            $tokenPart = explode('token=', $url)[1] ?? '';
            $parts = explode('.', $tokenPart);
            if (count($parts) >= 2) {
                $payload = base64_decode(strtr($parts[1], '-_', '+/'));
                if ($payload) {
                    $data = json_decode($payload, true);
                    if (! empty($data['url'])) {
                        return $data['url'];
                    }
                }
            }
        }

        return $url;
    }

    /**
     * Fetch TikTok download links via ssstik.io scraping.
     */
    private function fetchTiktok(Request $request): JsonResponse
    {
        $url = $request->input('url', '');

        try {
            // 1. Get the homepage to retrieve the 'tt' token and cookies
            $initialResponse = Http::timeout(15)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                    'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                    'Accept-Language' => 'en-US,en;q=0.5',
                ])
                ->get('https://ssstik.io/en');

            if (! $initialResponse->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Could not connect to TikTok download service. Please try again later.',
                ], 502);
            }

            $html = $initialResponse->body();
            $cookies = $initialResponse->cookies();

            // Extract the 'tt' token: s_tt = 'TOKEN_VALUE'
            $tt = '';
            if (preg_match('/s_tt\s*=\s*[\'"]([^\'"]+)[\'"]/', $html, $matches)) {
                $tt = $matches[1];
            } elseif (preg_match('/tt\s*:\s*[\'"]([^\'"]+)[\'"]/', $html, $matches)) {
                $tt = $matches[1];
            }

            if (empty($tt)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to initialize TikTok download session. Please try again.',
                ]);
            }

            // 2. POST to /abc?url=dl with htmx headers
            $response = Http::timeout(25)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                    'Accept' => '*/*',
                    'Accept-Language' => 'en-US,en;q=0.5',
                    'Referer' => 'https://ssstik.io/en',
                    'hx-request' => 'true',
                    'hx-target' => 'target',
                    'hx-current-url' => 'https://ssstik.io/en',
                    'Content-Type' => 'application/x-www-form-urlencoded; charset=UTF-8',
                ])
                ->withCookies($cookies->toArray(), 'ssstik.io')
                ->asForm()
                ->post('https://ssstik.io/abc?url=dl', [
                    'id' => $url,
                    'locale' => 'en',
                    'tt' => $tt,
                ]);

            if (! $response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'TikTok download service returned an error. Check the URL and try again.',
                ], 502);
            }

            $responseHtml = $response->body();

            // 3. Parse download options
            $items = $this->parseTiktokItems($responseHtml);

            if (empty($items)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No downloadable media found. Make sure the TikTok video is public.',
                ]);
            }

            return response()->json([
                'success' => true,
                'items' => $items,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing the TikTok video: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Parse TikTok download items from ssstik.io response HTML.
     */
    private function parseTiktokItems(string $html): array
    {
        $items = [];

        $dom = new \DOMDocument;
        @$dom->loadHTML('<?xml encoding="utf-8" ?><div>' . $html . '</div>');
        $xpath = new \DOMXPath($dom);

        // Find cover/thumbnail image
        $thumb = null;
        $imgs = $xpath->query('//img[@src]');
        foreach ($imgs as $img) {
            $src = $img->getAttribute('src');
            if ($src && ! str_contains($src, 'logosmall') && ! str_contains($src, 'favicon') && ! str_contains($src, 'apple-touch-icon')) {
                $thumb = $src;
                break;
            }
        }

        // Find description
        $title = 'TikTok Video';
        $paragraphs = $xpath->query('//p[contains(@class, "maintext")]');
        if ($paragraphs->length > 0) {
            $title = trim($paragraphs->item(0)->textContent);
        } else {
            $headings = $xpath->query('//h2 || //h3 || //p');
            foreach ($headings as $h) {
                $text = trim($h->textContent);
                if (! empty($text) && strlen($text) > 10 && ! str_contains($text, 'How to download') && ! str_contains($text, 'SSSTik')) {
                    $title = $text;
                    break;
                }
            }
        }

        // Find all download links (a tags)
        $links = $xpath->query('//a[@href]');
        foreach ($links as $a) {
            $href = $a->getAttribute('href');
            $label = trim($a->textContent);

            if (empty($href) || $href === '/' || str_contains($href, 'facebook.com') || str_contains($href, 'twitter.com') || str_contains($href, 'play.google.com') || str_contains($href, 'apps.apple.com')) {
                continue;
            }

            if (str_starts_with($href, '/')) {
                $href = 'https://ssstik.io' . $href;
            }

            if (empty($label)) {
                $label = 'Download';
            }

            $ext = 'mp4';
            if (str_contains(strtolower($label), 'mp3') || str_contains(strtolower($href), '.mp3')) {
                $ext = 'mp3';
            }

            $cleanTitle = preg_replace('/[^a-zA-Z0-9_-]/', '_', substr($title, 0, 30));
            $cleanTitle = preg_replace('/__+/', '_', $cleanTitle);
            $cleanTitle = trim($cleanTitle, '_');

            $cleanLabel = preg_replace('/[^a-zA-Z0-9_-]/', '_', strtolower($label));
            $cleanLabel = preg_replace('/__+/', '_', $cleanLabel);
            $cleanLabel = trim($cleanLabel, '_');

            $filename = 'tiktok_' . ($cleanTitle ?: 'video') . '_' . ($cleanLabel ?: 'download') . '.' . $ext;

            $items[] = [
                'thumb' => $thumb,
                'url' => $href,
                'filename' => $filename,
                'label' => $label,
            ];
        }

        return $items;
    }
}
