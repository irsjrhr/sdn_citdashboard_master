<?php
// database/migrations/2025_01_01_000002_add_activity_type_id_to_activities_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            // kalau sebelumnya ada kolom string activity_type, boleh dibiarkan dulu
            // atau di-drop setelah data dipindahkan.
            $table->foreignId('activity_type_id')
                  ->nullable()
                  ->after('location')
                  ->constrained('activity_types');
        });
    }

    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->dropConstrainedForeignId('activity_type_id');
        });
    }
};
