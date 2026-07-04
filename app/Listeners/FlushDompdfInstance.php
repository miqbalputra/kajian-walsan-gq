<?php

namespace App\Listeners;

use Laravel\Octane\Events\RequestTerminated;

/**
 * Flush DOMPDF bindings setelah setiap request.
 *
 * barryvdh/laravel-dompdf mendaftarkan beberapa singleton di container.
 * Di Octane (long-running process), instance yang sama dipakai ulang antar-request
 * yang dapat menyebabkan bocoran CSS/HTML dari request sebelumnya,
 * atau memory leak saat generate PDF kartu identitas / laporan.
 */
class FlushDompdfInstance
{
    /**
     * Container bindings yang terkait dengan DOMPDF.
     */
    protected array $bindings = [
        'dompdf',
        'dompdf.wrapper',
        'dompdf.options',
        'Barryvdh\DomPDF\PDF',
        Barryvdh\DomPDF\PDF::class,
    ];

    public function handle(RequestTerminated $event): void
    {
        foreach ($this->bindings as $binding) {
            if (app()->resolved($binding)) {
                app()->forgetInstance($binding);
            }
        }
    }
}