<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Hermes Agent Secret
    |--------------------------------------------------------------------------
    |
    | Set this to a long random value in production. Hermes must send it using
    | the X-Hermes-Secret header, Bearer token, or a secret request field.
    |
    */
    'secret' => env('HERMES_AGENT_SECRET'),
];
