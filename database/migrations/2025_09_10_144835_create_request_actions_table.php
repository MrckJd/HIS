<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('request_actions', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('action_type');
            $table->text('reason');
            $table->string('document')->nullable();
            $table->string('status')->default('Pending');
            $table->json('meta')->nullable();
            $table->foreignUlid('member_id')->nullable()->constrained('members')->nullOnDelete()->cascadeOnUpdate();
            $table->foreignUlid('user_id')->nullable()->constrained('users')->nullOnDelete()->cascadeOnUpdate();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('request_actions');
    }
};
