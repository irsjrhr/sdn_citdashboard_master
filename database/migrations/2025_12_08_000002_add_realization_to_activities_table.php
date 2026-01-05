<?php
// database/migrations/xxxx_xx_xx_xxxxxx_add_realization_to_activities_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->string('realization_status')->nullable(); // realized / not_realized
            $table->dateTime('realization_at')->nullable();   // waktu input realisasi
            $table->text('realization_note')->nullable();     // keterangan / alasan
        });
    }

    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->dropColumn(['realization_status', 'realization_at', 'realization_note']);
        });
    }
};