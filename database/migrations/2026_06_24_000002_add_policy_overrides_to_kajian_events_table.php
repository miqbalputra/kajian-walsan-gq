<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Menambahkan kolom policy_overrides (JSON) ke kajian_events.
 *
 * Setiap event bisa punya aturan presensi sendiri yang di-toggle
 * on/off via admin UI. Contoh:
 * {"online_enabled": true, "izin_requires_proof": true, ...}
 *
 * Jika policy_overrides NULL/kosong, sistem pakai default dari
 * config/event_categories.php sesuai kolom category event.
 *
 * Data lama: policy_overrides = NULL → pakai default kategori.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kajian_events', function (Blueprint $table) {
            $table->json('policy_overrides')->nullable()->after('category');
        });
    }

    public function down(): void
    {
        Schema::table('kajian_events', function (Blueprint $table) {
            $table->dropColumn('policy_overrides');
        });
    }
};