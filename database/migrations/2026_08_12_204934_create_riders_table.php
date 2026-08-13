<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('riders', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('phone_number')->nullable();
            $table->string('password');
            $table->string('profile_picture_url')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->string('vehicle_type')->nullable();
            $table->string('vehicle_registration_number')->nullable();
            $table->string('license_number')->nullable();
            $table->date('license_expiry')->nullable();
            $table->decimal('rating', 3, 2)->default(0.00);
            $table->integer('total_deliveries')->default(0);
            $table->decimal('total_earnings', 10, 2)->default(0.00);
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
             $table->enum('verification_status', [
        'pending',
        'verified',
        'rejected'
    ])->default('pending');
    $table->timestamp('last_location_update')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riders');
    }
};
