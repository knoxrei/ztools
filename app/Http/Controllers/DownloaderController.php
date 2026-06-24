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
        ]);

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
                    'url' => $validated['url'],
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

        try {
            $img = Http::timeout(15)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                    'Referer' => 'https://www.instagram.com/',
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

        try {
            $response = Http::timeout(60)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                    'Referer' => 'https://www.instagram.com/',
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
}
