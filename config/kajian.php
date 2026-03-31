<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application Settings
    |--------------------------------------------------------------------------
    |
    | Custom settings for the Kajian Walsan application.
    |
    */

    // Admin WhatsApp number for password reset requests
    // Format: 628xxxxxxxxxx (without + or spaces)
    'admin_whatsapp' => env('ADMIN_WHATSAPP', '6281234567890'),

    // Application name for display
    'name' => env('APP_KAJIAN_NAME', 'Kajian Walsan'),

    // Institution name
    'institution' => env('APP_INSTITUTION', 'Kelompok Tahfidz Griya Qur\'an "Tunas Ilmu"'),

];
