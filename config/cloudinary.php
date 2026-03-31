<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Cloudinary Configuration
    |--------------------------------------------------------------------------
    |
    | Konfigurasi untuk integrasi Cloudinary.
    | Daftar gratis di https://cloudinary.com
    | Setelah daftar, ambil Cloud Name, API Key, dan API Secret
    | dari Dashboard Cloudinary, lalu isi di file .env
    |
    */

    'cloud_name' => env('CLOUDINARY_CLOUD_NAME', ''),
    'api_key' => env('CLOUDINARY_API_KEY', ''),
    'api_secret' => env('CLOUDINARY_API_SECRET', ''),

    /*
    |--------------------------------------------------------------------------
    | Upload Options
    |--------------------------------------------------------------------------
    |
    | Folder tujuan upload di Cloudinary dan kualitas kompresi.
    |
    */

    'folder' => env('CLOUDINARY_FOLDER', 'kajian-walsan'),

    // Kualitas kompresi: 'auto:low', 'auto:eco', 'auto:good', 'auto:best'
    'quality' => 'auto:eco',

    // Max width/height (auto-resize jika lebih besar)
    'max_width' => 1200,
    'max_height' => 1200,

    // Aktifkan/nonaktifkan Cloudinary (false = simpan lokal seperti biasa)
    'enabled' => env('CLOUDINARY_ENABLED', false),
];
