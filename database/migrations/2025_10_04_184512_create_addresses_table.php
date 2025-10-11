<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();

            // Polymorphic (users / companies / lawyers ...)
            $table->string('addressable_type');
            $table->unsignedBigInteger('addressable_id');

            $table->string('title')->nullable();              // اسم مختصر للعنوان
            $table->string('building_number')->nullable();
            $table->string('apartment_number')->nullable();
            $table->string('floor_number')->nullable();

            $table->enum('type', ['home','work','headquarter','branch','office'])->nullable();

            $table->string('city')->nullable();
            $table->string('address_line')->nullable();

            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();

            $table->boolean('is_primary')->default(false);

            $table->timestamps();

            // فهارس
            $table->index(['addressable_type', 'addressable_id'], 'addresses_addressable_index');
        });
    }

    public function down(): void {
        Schema::dropIfExists('addresses');
    }
};
