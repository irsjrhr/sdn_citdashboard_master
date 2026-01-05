<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('sidia_approval_threads', function (Blueprint $table) {
            $table->id();
            $table->string('approval_no',20);
            $table->unsignedBigInteger('approver_id');
            $table->tinyInteger('status')->default(1); // 1=open, 2=closed
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('sidia_approval_threads');
    }
};