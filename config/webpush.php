<?php

return [
    /*
    |--------------------------------------------------------------------------
    | VAPID Keys for Web Push Notifications
    |--------------------------------------------------------------------------
    | Generate via: php artisan webpush:vapid
    | Or use online tool: https://vapidkeys.com/
    |
    | Setelah generate, tambahkan ke .env:
    | VAPID_PUBLIC_KEY=xxxxx
    | VAPID_PRIVATE_KEY=xxxxx
    | VAPID_SUBJECT=mailto:admin@domain.com
    */

    'vapid_public_key'  => env('VAPID_PUBLIC_KEY', ''),
    'vapid_private_key' => env('VAPID_PRIVATE_KEY', ''),
    'subject'           => env('VAPID_SUBJECT', 'mailto:admin@kajianwalsan.com'),
];
