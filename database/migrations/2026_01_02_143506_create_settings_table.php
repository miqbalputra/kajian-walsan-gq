<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('string'); // string, text, boolean, integer
            $table->string('group')->default('general'); // general, whatsapp, notification, etc.
            $table->string('label')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // Insert default settings
        DB::table('settings')->insert([
            [
                'key' => 'admin_whatsapp',
                'value' => '6281234567890',
                'type' => 'string',
                'group' => 'whatsapp',
                'label' => 'Nomor WhatsApp Admin',
                'description' => 'Nomor WhatsApp admin untuk menerima permintaan reset password. Format: 628xxxxxxxxxx (tanpa + atau spasi)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'institution_name',
                'value' => 'Kelompok Tahfidz Griya Qur\'an "Tunas Ilmu"',
                'type' => 'string',
                'group' => 'general',
                'label' => 'Nama Lembaga',
                'description' => 'Nama lembaga yang ditampilkan di aplikasi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'app_name',
                'value' => 'Kajian Walsan',
                'type' => 'string',
                'group' => 'general',
                'label' => 'Nama Aplikasi',
                'description' => 'Nama aplikasi yang ditampilkan di header',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
