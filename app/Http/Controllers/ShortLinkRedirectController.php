<?php

namespace App\Http\Controllers;

use App\Models\ShortLink;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ShortLinkRedirectController extends Controller
{
    public function handle(Request $request, $code)
    {
        $link = ShortLink::where('code', $code)->first();

        if (!$link) {
            abort(404, 'Short link not found or expired.');
        }

        // Enforce Network Connection Type
        $isClientOnTor = \Illuminate\Support\Str::endsWith($request->getHost(), '.onion') || $request->header('X-Tor-Onion') || env('APP_ENV') === 'tor';

        if ($link->connection_type === 'tor' && !$isClientOnTor) {
            abort(403, 'This short link is restricted to Tor network (.onion) connections only.');
        }

        if ($link->connection_type === 'clearnet' && $isClientOnTor) {
            abort(403, 'This short link is restricted to Clearnet connections only.');
        }


        // Check expiration by time
        if ($link->expires_at && $link->expires_at->isPast()) {
            $link->delete();
            abort(410, 'This short link has expired.');
        }

        // Check expiration by clicks
        if ($link->max_clicks && $link->clicks_count >= $link->max_clicks) {
            $link->delete();
            abort(410, 'This short link has reached its maximum click limit.');
        }

        // Handle Password Protection
        if ($link->password) {
            $submittedPassword = $request->input('password');
            
            if (!$submittedPassword || !Hash::check($submittedPassword, $link->password)) {
                // Return password prompt view
                return response()->view('pages.tools.shortlink-password', [
                    'code' => $code,
                    'error' => $submittedPassword ? 'Incorrect password. Access denied.' : null,
                    'cloak_title' => $link->cloak_title,
                    'cloak_desc' => $link->cloak_desc,
                ]);
            }
        }

        // Handle Cloaking / Intermediate Page
        if ($link->cloak_title || $link->cloak_desc) {
            $confirmed = $request->has('confirm');
            
            if (!$confirmed) {
                return response()->view('pages.tools.shortlink-cloak', [
                    'code' => $code,
                    'link' => $link,
                    'password' => $request->input('password'), // pass it forward
                ]);
            }
        }

        // Successful access: update clicks
        $link->increment('clicks_count');

        // Handle Burn-on-read
        if ($link->is_burn_after_use) {
            $link->delete();
        }

        return redirect()->away($link->original_url);
    }
}
