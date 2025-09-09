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
        Schema::create('member_services', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('member_id')->constrained('members')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignUlid('service_id')->constrained('services')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignUlid('user_id')->nullable()->constrained('users')->nullOnDelete()->cascadeOnUpdate();
            $table->dateTime('date_received');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('member_services');
    }
};
