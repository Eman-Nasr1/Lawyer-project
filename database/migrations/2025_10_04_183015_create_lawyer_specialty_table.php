<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('lawyer_specialty', function (Blueprint $table) {
            $table->id();

            $table->foreignId('lawyer_id')->constrained('lawyers')->cascadeOnDelete();
            $table->foreignId('specialty_id')->constrained('specialties')->cascadeOnDelete();

            $table->timestamps();

            $table->unique(['lawyer_id', 'specialty_id']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('lawyer_specialty');
    }
};
