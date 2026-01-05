<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('sidia_approval_approvers', function (Blueprint $table) {
            $table->id();
            $table->string('approval_no', 20)
                ->collation('utf8mb4_general_ci');
            $table->unsignedBigInteger('user_id');
            $table->string('role',50);
            $table->integer('approval_order');
            $table->tinyInteger('status')->default(0);
            $table->timestamp('approved_at')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();

            $table->foreign('approval_no')
                ->references('approval_no')
                ->on('sidia_approval')
                ->cascadeOnDelete();
        });
    }
    public function down(): void {
        Schema::dropIfExists('sidia_approval_approvers');
    }
};