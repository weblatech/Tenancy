<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Platform IP Address
    |--------------------------------------------------------------------------
    |
    | The server IP address that custom domains should point to via A record.
    | On production, set this to your server's public IP address.
    | On localhost, it auto-detects from the server.
    |
    */

    'ip' => env('PLATFORM_IP', 'Set in Render env'),

    /*
    |--------------------------------------------------------------------------
    | Platform Domain
    |--------------------------------------------------------------------------
    |
    | The main domain where the platform admin panel is hosted.
    | This is used for DNS instruction display and domain validation.
    |
    */

    'domain' => env('PLATFORM_DOMAIN', 'localhost'),

    /*
    |--------------------------------------------------------------------------
    | Storefront URL Pattern
    |--------------------------------------------------------------------------
    |
    | How tenant storefront URLs are constructed.
    | Options: 'subdomain' (tenant.localhost), 'path' (localhost/tenant)
    |
    */

    'url_pattern' => env('STORE_URL_PATTERN', 'subdomain'),

];
