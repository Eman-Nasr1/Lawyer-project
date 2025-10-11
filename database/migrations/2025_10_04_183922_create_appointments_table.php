<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();   // العميل
            $table->foreignId('lawyer_id')->constrained('lawyers')->cascadeOnDelete(); // المحامي
            $table->foreignId('company_id')->nullable()->constrained('companies')->cascadeOnDelete(); // الشركة (اختياري)

            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');

            $table->enum('status', ['pending','confirmed','cancelled','completed'])->default('pending');
            $table->text('notes')->nullable();
            $table->longText('attachments')->nullable(); // مرفقات كنص (ممكن لاحقاً نستعمل جدول منفصل)

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void {
        Schema::dropIfExists('appointments');
    }
};
