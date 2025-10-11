<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('legal_decisions', function (Blueprint $table) {
            $table->id();

            // تصنيف القرار
            $table->foreignId('category_id')->constrained('legal_decision_categories')->cascadeOnDelete();

            $table->string('title');          // عنوان القرار
            $table->longText('body');         // نص القرار
            $table->string('source_url')->nullable(); // رابط المصدر (اختياري)
            $table->timestamp('published_at')->nullable(); // تاريخ النشر (اختياري)

            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('legal_decisions');
    }
};
