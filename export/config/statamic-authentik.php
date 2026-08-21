<?php

return [

    /* Authentik credentials. */
    'authentik' => [
        'base_url' => env('AUTHENTIK_BASE_URL'),
        'client_id' => env('AUTHENTIK_CLIENT_ID'),
        'client_secret' => env('AUTHENTIK_CLIENT_SECRET'),
    ],

    'new_user_role' => env('AUTHENTIK_NEW_USER_ADMIN_ROLE'),
];
