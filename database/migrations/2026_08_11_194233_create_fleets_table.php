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
        Schema::create('fleets', function (Blueprint $table) {
            $table->id(); 
            $table->foreignId('manager_id')->constrained('users')->onDelete('cascade');
            $table->string('fleet_name');
            $table->string('email')->nullable()->unique();
            $table->text('description')->nullable();
            $table->string('address')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('phone_number')->nullable();
            $table->string('logo_url')->nullable();
            $table->enum('status', ['active', 'inactive','suspended'])->default('active');
             $table->enum('verification_status', [
        'pending',
        'verified',
        'rejected'
    ])->default('pending');
    $table->integer('total_deliveries')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fleets');
    }
};
