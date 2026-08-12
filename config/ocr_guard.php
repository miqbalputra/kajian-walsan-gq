<?php

return [
    'enabled' => (bool) env('OCR_GUARD_ENABLED', false),
    'url' => rtrim((string) env('OCR_GUARD_URL', 'http://ocr-guard:9005'), '/'),
    'token' => (string) env('OCR_GUARD_TOKEN', ''),
    'timeout' => (int) env('OCR_GUARD_TIMEOUT', 90),
    'connect_timeout' => (int) env('OCR_GUARD_CONNECT_TIMEOUT', 10),
    'shadow_mode' => (bool) env('OCR_GUARD_SHADOW_MODE', true),
    'model' => (string) env('OCR_GUARD_MODEL', 'PP-OCRv6-small-id'),
    'approve_note_min_chars' => (int) env('OCR_GUARD_APPROVE_NOTE_MIN_CHARS', 40),
    'approve_letter_min_chars' => (int) env('OCR_GUARD_APPROVE_LETTER_MIN_CHARS', 80),
    'approve_min_boxes' => (int) env('OCR_GUARD_APPROVE_MIN_BOXES', 2),
    'approve_min_confidence' => (int) env('OCR_GUARD_APPROVE_MIN_CONFIDENCE', 70),
    'reject_min_confidence' => (int) env('OCR_GUARD_REJECT_MIN_CONFIDENCE', 90),
    'max_raw_text' => (int) env('OCR_GUARD_MAX_RAW_TEXT', 1000),
];
