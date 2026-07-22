<?php

return [

    // Base URL of the Spurs admin control plane.
    'url' => env('SPURS_ADMIN_URL', 'http://127.0.0.1:3300'),

    // This app's slug in the admin registry (accounts, pay, wallet, ...).
    'app' => env('SPURS_ADMIN_APP'),

    // Bootstrap secret used to authenticate to the admin config API
    // (sent as the x-admin-bootstrap header). This is the single secret an app
    // needs in its own .env; everything else can come from admin.
    'secret' => env('SPURS_ADMIN_SECRET'),

    // Seconds to cache the fetched config. Keeps boot fast and survives brief
    // admin downtime.
    'cache_ttl' => (int) env('SPURS_ADMIN_CACHE_TTL', 300),

    // If admin is unreachable or a key is missing, fall back to env() / existing
    // config rather than breaking the app. Strongly recommended.
    'fallback' => (bool) env('SPURS_ADMIN_FALLBACK', true),

    // Map admin credential keys onto Laravel config paths. When admin returns a
    // mapped key, it OVERRIDES the local value — making admin the source of
    // truth for these. Anything not returned keeps its .env value.
    'map' => [
        // 'INTERNAL_API_SECRET' => 'spurs.internal_secret',
        // 'SPURS_GEOIP_URL'     => 'spurs.geoip_url',
    ],

];
