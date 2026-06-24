<?php

namespace App\Http\Controllers;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
     * Parse download items (thumbnail + download link) from Downloadgram HTML response.
     *
     * @return array<int, array{thumb: string|null, url: string, filename: string}>
     */
    private function parseDownloadItems(string $html): array
    {
        $items = [];

        // Suppress libxml errors for invalid HTML
        $dom = new \DOMDocument;
        @$dom->loadHTML('<?xml encoding="utf-8" ?>'.$html);
        $xpath = new \DOMXPath($dom);

        // Find the #downloadhere container
        $downloadHere = $xpath->query('//*[@id="downloadhere"]');
        if ($downloadHere->length === 0) {
            return [];
        }

        // Each .row inside #downloadhere is one media item
        $rows = $xpath->query('.//*[contains(@class,"row")]', $downloadHere->item(0));

        foreach ($rows as $row) {
            $item = [
                'thumb' => null,
                'url' => '',
                'filename' => 'download',
            ];

            // Thumbnail <img>
            $imgs = $xpath->query('.//img', $row);
            if ($imgs->length > 0) {
                $item['thumb'] = $imgs->item(0)->getAttribute('src');
            }

            // Download <a> with download attribute
            $links = $xpath->query('.//a[@download]', $row);
            if ($links->length > 0) {
                $a = $links->item(0);
                $item['url'] = $a->getAttribute('href');
                $text = trim($a->textContent);
                $item['label'] = $text ?: 'DOWNLOAD';

                // Derive a clean filename from the CDN token if possible
                if ($item['url']) {
                    $item['filename'] = basename(parse_url($item['url'], PHP_URL_PATH)) ?: 'download';
                }
            }

            if ($item['url']) {
                $items[] = $item;
            }
        }

        return $items;
    }
}
