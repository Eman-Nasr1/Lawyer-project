<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('lawyer_availabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lawyer_id')->constrained('lawyers')->cascadeOnDelete();

            $table->enum('day_of_week', ['sunday','monday','tuesday','wednesday','thursday','friday','saturday'])->nullable();
            $table->date('date')->nullable();

            $table->time('start_time');
            $table->time('end_time');

            $table->boolean('is_active')->default(true);
           

            $table->timestamps();
            $table->softDeletes();

            $table->index(['lawyer_id','day_of_week','date']);
        });
    }
    public function down(): void { Schema::dropIfExists('lawyer_availabilities'); }
};
