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
        Schema::create('fleet_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fleet_id')->constrained('fleets')->onDelete('cascade');
            $table->foreignId('rider_id')->constrained('riders')->onDelete('cascade');
            $table->enum('role', ['Manager', 'member'])->default('member');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamp('joined_at')->useCurrent();
            $table->timestamps();
            $table->unique(['fleet_id', 'rider_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fleet_members');
    }
};
