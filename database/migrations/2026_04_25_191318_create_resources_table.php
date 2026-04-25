<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')
                  ->constrained('class_sessions')
                  ->onDelete('cascade');
            $table->foreignId('teacher_id')
                  ->constrained('users')
                  ->onDelete('cascade');
            $table->string('title');
            $table->enum('type', ['link', 'file']);
            $table->string('url')->nullable();
            $table->string('file_path')->nullable();
            $table->enum('rendering_mode', ['iframe', 'pdfjs', 'reader', 'external'])
                  ->default('iframe');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resources');
    }
};