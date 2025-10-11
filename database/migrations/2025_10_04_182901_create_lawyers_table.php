<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('lawyers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            $table->string('professional_card_image')->nullable();
            $table->unsignedSmallInteger('years_of_experience')->default(0);
            $table->text('bio')->nullable();

            $table->decimal('avg_rating', 3, 2)->default(0.00);
            $table->unsignedInteger('reviews_count')->default(0);

            $table->boolean('is_approved')->default(false);
            $table->boolean('is_featured')->default(false);

            $table->longText('admin_notes')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('lawyers');
    }
};
