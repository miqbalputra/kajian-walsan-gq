<?php

return [

    'class_namespace' => 'App\\Livewire',

    'view_path' => resource_path('views/livewire'),

    'layout' => 'components.layouts.app',

    'lazy_placeholder' => null,

    /*
    |---------------------------------------------------------------------------
    | Temporary File Uploads
    |---------------------------------------------------------------------------
    |
    | Livewire menyimpan file yang diupload sementara di disk lokal sebelum
    | diproses. Konfigurasi ini memastikan direktori tmp ada dan rule-nya
    | sesuai dengan kebutuhan upload bukti & surat izin (max 10MB).
    |
    */

    'temporary_file_upload' => [
        'disk'       => 'local',        // Gunakan disk 'local' (storage/app/private)
        'rules'      => ['required', 'file', 'max:10240'], // 10MB max
        'directory'  => 'livewire-tmp', // storage/app/private/livewire-tmp
        'middleware' => 'throttle:60,1',
        'preview_mimes' => [
            'png', 'gif', 'bmp', 'svg', 'wav', 'mp4',
            'mov', 'avi', 'wmv', 'mp3', 'm4a',
            'jpg', 'jpeg', 'mpga', 'webp', 'wma',
        ],
        'max_upload_time' => 5,
        'cleanup'    => true,
    ],

    'render_on_redirect' => false,

    'legacy_model_binding' => false,

    'inject_assets' => true,

    'navigate' => [
        'show_progress_bar' => true,
        'progress_bar_color' => '#2299dd',
    ],

    'inject_morph_markers' => true,

    'smart_wire_keys' => false,

    'pagination_theme' => 'tailwind',

    'release_token' => 'a',
];
