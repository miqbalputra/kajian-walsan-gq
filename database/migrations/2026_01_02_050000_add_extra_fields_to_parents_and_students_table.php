<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('parents', function (Blueprint $table) {
            $table->boolean('is_single_parent')->default(false)->after('type');
        });

        Schema::table('students', function (Blueprint $table) {
            $table->string('guardian_name', 100)->nullable()->after('address');
            $table->string('guardian_phone', 20)->nullable()->after('guardian_name');
            $table->string('guardian_relationship', 50)->nullable()->after('guardian_phone');
        });
    }

    public function down(): void
    {
        Schema::table('parents', function (Blueprint $table) {
            $table->dropColumn('is_single_parent');
        });

        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['guardian_name', 'guardian_phone', 'guardian_relationship']);
        });
    }
};
