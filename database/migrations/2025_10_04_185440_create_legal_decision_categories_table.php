<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('legal_decision_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();  // اسم التصنيف (جنائي، مدني...)
            $table->string('slug')->unique();  // slug للرابط
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('legal_decision_categories');
    }
};
