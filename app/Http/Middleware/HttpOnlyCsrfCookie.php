<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;

class HttpOnlyCsrfCookie extends ValidateCsrfToken
{
    protected function newCookie($request, $config)
    {
        // This app sends CSRF tokens through forms, Livewire, and the meta header.
        // Keep the existing cookie scope/value while removing JavaScript access.
        return parent::newCookie($request, $config)->withHttpOnly(true);
    }
}
