<?php

return [
    /*
     * Kajian only consumes this API. The master application remains the
     * authoritative place for changes to students and guardians.
     */
    'enabled' => (bool) env('DATA_INDUK_ENABLED', false),

    'base_url' => rtrim((string) env('DATA_INDUK_BASE_URL', ''), '/'),
    'api_key' => (string) env('DATA_INDUK_API_KEY', ''),
    'unit_id' => (string) env('DATA_INDUK_UNIT_ID', ''),
    'timeout' => (int) env('DATA_INDUK_TIMEOUT', 10),

    /*
     * JSON object mapping master class UUIDs to existing local `classes.id`.
     * Example: {"7fc6...": 12}
     */
    'class_map' => json_decode((string) env('DATA_INDUK_CLASS_MAP', '{}'), true) ?: [],
];
