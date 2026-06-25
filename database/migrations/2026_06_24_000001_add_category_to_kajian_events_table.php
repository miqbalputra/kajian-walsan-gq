<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Menambahkan kolom `category` ke tabel kajian_events.
 *
 * Tujuan: Generalisasi kegiatan — kajian, rapor, pertemuan, dll —
 * dalam satu tabel dengan kategori yang punya aturan berbeda.
 *
 * PENTING: Default 'kajian' agar semua event yang sudah ada
 * (termasuk yang dipakai untuk pembagian rapor sebelumnya)
 * tetap berfungsi identik. Admin bisa mengubah kategori event
 * via UI setelah deploy.
 *
 * Data TIDAK dihapus, TIDAK diubah — hanya menambah kolom baru.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kajian_events', function (Blueprint $table) {
            $table->string('category', 50)->default('kajian')->after('status');
            $table->index('category');
        });

        // Semua event yang sudah ada otomatis dapat category='kajian'
        // karena default value di migration.
        // Admin bisa re-categorize event rapor/pertemuan via UI nanti.
    }

    public function down(): void
    {
        Schema::table('kajian_events', function (Blueprint $table) {
            $table->dropIndex(['category']);
            $table->dropColumn('category');
        });
    }
};