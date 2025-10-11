<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('appointment_cancellations', function (Blueprint $table) {
            $table->id();

            $table->foreignId('appointment_id')->constrained('appointments')->cascadeOnDelete();
            $table->unsignedBigInteger('cancelled_by'); // user_id أو lawyer_id (حسب السياق)
            $table->text('reason')->nullable();
            $table->timestamp('cancelled_at')->useCurrent();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['appointment_id', 'cancelled_by']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('appointment_cancellations');
    }
};
