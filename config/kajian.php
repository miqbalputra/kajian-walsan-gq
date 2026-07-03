<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application Settings
    |--------------------------------------------------------------------------
    |
    | Custom settings for the Presensi Wali Santri application.
    |
    */

    // Admin WhatsApp number for password reset requests
    // Format: 628xxxxxxxxxx (without + or spaces)
    'admin_whatsapp' => env('ADMIN_WHATSAPP', '6281234567890'),

    // Application name for display
    'name' => env('APP_KAJIAN_NAME', 'Presensi Wali Santri'),

    // Institution name
    'institution' => env('APP_INSTITUTION', 'Kelompok Tahfidz Griya Qur\'an "Tunas Ilmu"'),

];
