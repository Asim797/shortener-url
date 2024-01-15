<?php

return [
    // Google Safe Browsing API key for authentication
    'google_safe_browsing_api_key' => env('GOOGLE_SAFE_BROWSING_API_KEY', ''),

    // Google Safe Browsing API endpoint URL
    'google_safe_browsing_api_url' => env('GOOGLE_SAFE_BROWSING_API_URL', 'https://safebrowsing.googleapis.com/')
];