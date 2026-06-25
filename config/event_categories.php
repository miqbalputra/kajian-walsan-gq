<?php

/**
 * Konfigurasi aturan presensi per kategori kegiatan.
 *
 * Setiap kategori mendefinisikan:
 * - label: nama tampilan
 * - statuses: status presensi yang diizinkan
 * - online_enabled: apakah hadir_online tersedia
 * - online_requires_proof: hadir_online wajib upload bukti
 * - izin_requires_proof: izin wajib upload surat
 * - izin_requires_notes: izin wajib ada catatan alasan
 * - guru_hadir_fisik_requires_proof: guru hadir fisik wajib upload catatan
 * - proof_folders: folder Cloudinary per jenis bukti
 * - ai_review: aktifkan AI review otomatis untuk bukti upload
 *
 * Tambah kategori baru cukup tambah entry di array ini.
 */

return [

    'kajian' => [
        'label' => 'Kajian Wali Santri',
        'statuses' => ['hadir_fisik', 'hadir_online', 'izin', 'alpha'],
        'online_enabled' => true,
        'online_requires_proof' => true,
        'izin_requires_proof' => true,
        'izin_requires_notes' => true,
        'guru_hadir_fisik_requires_proof' => true,
        'proof_folders' => [
            'attendance' => 'attendance-proofs',
            'teacher_notes' => 'teacher-attendance-notes',
            'izin' => 'izin-documents',
            'teacher_izin' => 'teacher-permission-letters',
        ],
        'ai_review' => true,
    ],

    'rapor' => [
        'label' => 'Pembagian Rapor',
        'statuses' => ['hadir_fisik', 'izin', 'alpha'],
        'online_enabled' => false,
        'online_requires_proof' => false,
        'izin_requires_proof' => true,
        'izin_requires_notes' => true,
        'guru_hadir_fisik_requires_proof' => false,
        'proof_folders' => [
            'attendance' => 'rapor-attendance',
            'izin' => 'rapor-permission-letters',
        ],
        'ai_review' => false,
    ],

    'pertemuan' => [
        'label' => 'Pertemuan Wali Santri',
        'statuses' => ['hadir_fisik', 'izin', 'alpha'],
        'online_enabled' => false,
        'online_requires_proof' => false,
        'izin_requires_proof' => true,
        'izin_requires_notes' => true,
        'guru_hadir_fisik_requires_proof' => false,
        'proof_folders' => [
            'attendance' => 'pertemuan-attendance',
            'izin' => 'pertemuan-permission-letters',
        ],
        'ai_review' => false,
    ],

];