<?php
// database/migrations/xxxx_xx_xx_xxxxxx_add_type_and_focus_to_activities_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->string('activity_type')->default('Meeting'); // Meeting / Visit
            $table->boolean('is_focus')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->dropColumn(['activity_type', 'is_focus']);
        });
    }
};