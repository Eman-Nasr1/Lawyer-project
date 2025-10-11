<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('cases', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();   // العميل
            $table->foreignId('lawyer_id')->constrained('lawyers')->cascadeOnDelete(); // المحامي
            $table->foreignId('company_id')->nullable()->constrained('companies')->cascadeOnDelete();

            $table->string('title');
            $table->enum('case_type', [
                'criminal','civil','family','commercial',
                'labor','administrative','tax','real-estate',
                'ip','immigration','arbitration','other'
            ])->default('other');

            $table->text('description')->nullable();
            $table->longText('attachments')->nullable();

            $table->enum('status', ['ongoing','completed','cancelled'])->default('ongoing');
            $table->longText('stages')->nullable(); // json stages

            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();

            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void {
        Schema::dropIfExists('cases');
    }
};
