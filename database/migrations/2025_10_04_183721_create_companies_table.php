<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // مالك الشركة/المسؤول
            $table->string('name');
            $table->string('slug')->unique();

            $table->string('logo')->nullable();

            $table->string('city')->nullable();
            $table->string('address')->nullable();

            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();

            $table->string('phone')->nullable();
            $table->string('email')->nullable();

            $table->text('description')->nullable();

            $table->decimal('avg_rating', 3, 2)->default(0.00);
            $table->unsignedInteger('reviews_count')->default(0);

            $table->boolean('is_approved')->default(false);
            $table->boolean('is_featured')->default(false);

            $table->timestamps();

            $table->index(['user_id', 'city']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('companies');
    }
};
