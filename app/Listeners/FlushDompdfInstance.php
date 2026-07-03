<?php

namespace App\Listeners;

use Laravel\Octane\Events\RequestTerminated;

/**
 * Flush DOMPDF singleton instance setelah setiap request.
 *
 * barryvdh/laravel-dompdf mendaftarkan DOMPDF sebagai singleton di container.
 * Di Octane (long-running process), instance yang sama dipakai ulang antar-request
 * yang dapat menyebabkan bocoran CSS/HTML dari request sebelumnya.
 * Listener ini memastikan setiap request PDF mendapat instance fresh.
 */
class FlushDompdfInstance
{
    public function handle(RequestTerminated $event): void
    {
        if (app()->resolved('dompdf')) {
            app()->forgetInstance('dompdf');
        }
    }
}