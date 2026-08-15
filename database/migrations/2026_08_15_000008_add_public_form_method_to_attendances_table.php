<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        DB::statement("ALTER TABLE attendances MODIFY method ENUM('scan_qr','manual','upload','google_form','public_form') NOT NULL DEFAULT 'scan_qr'");
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        DB::statement("UPDATE attendances SET method = 'manual' WHERE method = 'public_form'");
        DB::statement("ALTER TABLE attendances MODIFY method ENUM('scan_qr','manual','upload','google_form') NOT NULL DEFAULT 'scan_qr'");
    }
};
