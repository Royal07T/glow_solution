<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Trusted Proxies
    |--------------------------------------------------------------------------
    |
    | Comma-separated proxy IPs or CIDRs allowed to send forwarded headers.
    | Use "*" to trust all proxies in local environments. Leave empty when
    | the application is accessed directly.
    |
    */

    'proxies' => env('TRUSTED_PROXIES') ?: null,

];
