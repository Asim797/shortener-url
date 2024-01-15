<?php

namespace App\Http\Controllers;

use App\Models\Url;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Http\RedirectResponse;

class UrlController extends Controller
{
    /**
     * Create a short url
     *
     * @param Request $request as Array
     * @return object
     */
    public function shortenUrl(Request $request): object
    {
        $request->validate([
            'original_url' => 'required',
        ]);
        $existingUrl = Url::where('original_url', $request->input('original_url'))->first();
        if (!empty($existingUrl)) {
            return response()->json(['shortened_url' => $existingUrl->short_url], 200);
        }

        $isSafe = $this->checkSafeBrowsingApi($request->original_url);
        if (empty($isSafe)) {
            return response()->json(['error' => 'Unsafe URL'], 400);
        }

        $hash = Str::random(6);
        $url = Url::create([
            'original_url' => $request->original_url,
            'short_url'    => config('app.url') . '/open/' . $hash,
            'hash'         => $hash,
        ]);
        return response()->json(['shortened_url' => $url->short_url], 201);
    }

    /**
     * redirect to original url
     *
     * @param string $hash
     * @return RedirectResponse
     */
    public function redirectToOriginalUrl(string $hash): RedirectResponse
    {
        $url = Url::where('hash', $hash)->firstOrFail();
        return redirect($url->original_url);
    }

    /**
     * Check the provided URL against the Google Safe Browsing API for potential threats.
     *
     * @param string $url The URL to check.
     *
     * @return bool Returns true if the URL is considered safe, false if it matches a threat.
     */
    private function checkSafeBrowsingApi($url): bool
    {
        $apiKey = config('url_short.google_safe_browsing_api_key');
        $apiEndpoint = config('url_short.google_safe_browsing_api_url').'v4/threatMatches:find';
        $body = [
            'client' => [
                'clientId' => 'asimrazatariq',
                'clientVersion' => '1.5.2',
            ],
            'threatInfo' => [
                'threatTypes' => ['MALWARE', 'SOCIAL_ENGINEERING'],
                'platformTypes' => ['WINDOWS'],
                'threatEntryTypes' => ['URL'],
                'threatEntries' => [['url' => $url]],
            ]
        ];
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post("$apiEndpoint?key=$apiKey", $body);
            $responseData = $response->json();
            return empty($responseData['matches']);
        }catch (\Exception $e){
            return false;
        }
    }
}
