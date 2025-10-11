<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('appointment_files', function (Blueprint $table) {
            $table->id();

            $table->foreignId('appointment_id')->constrained('appointments')->cascadeOnDelete();
            $table->unsignedBigInteger('uploader_id'); // المستخدم اللي رفع الملف

            $table->string('name');
            $table->string('path'); // مسار الملف

            $table->timestamps();
            $table->softDeletes();

            $table->index(['appointment_id','uploader_id']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('appointment_files');
    }
};
