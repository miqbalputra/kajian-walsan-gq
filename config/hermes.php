<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Hermes Agent Secret
    |--------------------------------------------------------------------------
    |
    | Set this to a long random value in production. Hermes must send it using
    | the X-Hermes-Secret header or Bearer token. Do not put secrets in query
    | strings or request bodies because infrastructure logs may retain them.
    |
    */
    'secret' => env('HERMES_AGENT_SECRET'),
];
