<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('company_lawyer', function (Blueprint $table) {
            $table->id();

            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('lawyer_id')->constrained('lawyers')->cascadeOnDelete();

            $table->string('title')->nullable();     // المسمّى داخل الشركة (شريك / مستشار ...)
            $table->boolean('is_primary')->default(false); // محامي أساسي بالشركة

            $table->timestamps();

            $table->index(['company_id', 'lawyer_id']);
            // لو عايزة تمنعي تكرار نفس المحامي في نفس الشركة، فعّلي السطر التالي بدلاً من الـ index:
             $table->unique(['company_id', 'lawyer_id']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('company_lawyer');
    }
};
