<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('favorites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            // polymorphic target (we'll store Lawyer here)
            $table->string('favoritable_type');
            $table->unsignedBigInteger('favoritable_id');

            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id','favoritable_type','favoritable_id']);
            $table->unique(['user_id','favoritable_type','favoritable_id'], 'favorites_unique');
        });
    }
    public function down(): void {
        Schema::dropIfExists('favorites');
    }
};
