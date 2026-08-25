<?php

use JustBetter\HealthChecks\Http\Middleware\IpWhitelistMiddleware;

return [
    'schedule' => true,

    'middleware' => [
        IpWhitelistMiddleware::class,
    ],

    'ip_whitelist' => env('HEALTHCHECK_IPS'),
];
