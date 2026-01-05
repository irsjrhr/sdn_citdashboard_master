<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('sidia_approval_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('thread_id')->constrained('sidia_approval_threads')->cascadeOnDelete();
            $table->string('approval_no',20);
            $table->enum('sender_role',['CREATOR','APPROVER']);
            $table->unsignedBigInteger('sender_id');
            $table->text('message');
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('sidia_approval_messages');
    }
};