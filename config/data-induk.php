<?php

return [
    /*
     * Kajian only consumes this API. The master application remains the
     * authoritative place for changes to students and guardians.
     */
    'enabled' => filter_var(env('DATA_INDUK_ENABLED', false), FILTER_VALIDATE_BOOLEAN),

    'base_url' => rtrim((string) env('DATA_INDUK_BASE_URL', ''), '/'),
    'api_key' => (string) env('DATA_INDUK_API_KEY', ''),
    'unit_id' => (string) env('DATA_INDUK_UNIT_ID', ''),
    'timeout' => (int) env('DATA_INDUK_TIMEOUT', 10),
    'scheduled' => filter_var(env('DATA_INDUK_SYNC_SCHEDULED', false), FILTER_VALIDATE_BOOLEAN),

    // Optional pilot mapping for master class IDs that have not been linked
    // locally yet. JSON example: {"master-class-uuid": 12}
    'class_map' => json_decode((string) env('DATA_INDUK_CLASS_MAP', '{}'), true) ?: [],

];
