<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('otps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->string('channel')->default('email'); // email|phone
            $table->string('purpose')->default('reset'); // reset|login|verify
            $table->string('code_hash');                 // تخزين الكود مُشفّر
            $table->timestamp('expires_at');
            $table->timestamp('consumed_at')->nullable();
            $table->unsignedTinyInteger('attempts')->default(0);

            $table->string('otp_token')->nullable();     // يصدر بعد التحقق
            $table->timestamp('otp_token_expires_at')->nullable();

            $table->timestamps();

            $table->index(['user_id', 'purpose']);
            $table->index(['otp_token']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('otps');
    }
};

