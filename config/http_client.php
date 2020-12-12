<?php

/**
 * HTTP client settings
 */
return [
    'allow_redirects' => [
        'max'             => 5,
        'strict'          => false,
        'referer'         => false,
        'protocols'       => ['http', 'https'],
        'track_redirects' => true
    ],
    'headers' => [
        'User-Agent' => config('app.name') . '/' . config('app.version')
    ],
    // For security reasons, SSL certificate verification should be enabled.
    // However, document's served through a connection using a self-signed
    // certification will be marked as unreachable.
    'verify' => false
];
