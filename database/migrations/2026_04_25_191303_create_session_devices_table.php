<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('session_devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')
                  ->constrained('class_sessions')
                  ->onDelete('cascade');
            $table->foreignId('device_id')
                  ->constrained()
                  ->onDelete('cascade');
            $table->timestamp('joined_at')->useCurrent();
            $table->timestamp('left_at')->nullable();
            $table->boolean('is_locked')->default(false);
            $table->unsignedTinyInteger('violation_count')->default(0);

            $table->unique(['session_id', 'device_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('session_devices');
    }
};