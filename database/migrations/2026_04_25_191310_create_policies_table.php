<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('policies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('classroom_id')
                  ->constrained()
                  ->onDelete('cascade');
            $table->string('name');
            $table->json('allowed_urls')->nullable();
            $table->json('blocked_keywords')->nullable();
            $table->boolean('internet_access')->default(true);
            $table->boolean('is_default')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('policies');
    }
};