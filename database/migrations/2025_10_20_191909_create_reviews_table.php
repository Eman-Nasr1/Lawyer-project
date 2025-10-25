<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();

            $table->foreignId('appointment_id')->constrained('appointments')->cascadeOnDelete();
            $table->foreignId('reviewer_id')->constrained('users')->cascadeOnDelete();

            // polymorphic target (we’ll review Lawyer now)
            $table->string('reviewable_type');
            $table->unsignedBigInteger('reviewable_id');

            $table->unsignedTinyInteger('rating'); // 1..5
            $table->text('comment')->nullable();
            $table->timestamp('posted_at')->useCurrent();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['reviewable_type','reviewable_id']);
            $table->unique(['appointment_id','reviewer_id'], 'reviews_unique_per_appointment');
        });
    }
    public function down(): void {
        Schema::dropIfExists('reviews');
    }
};
