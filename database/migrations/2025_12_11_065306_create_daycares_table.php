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
        Schema::create('daycares', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('address');
            $table->string('city');
            $table->string('postal_code', 20);
            $table->string('country')->default('France');
            $table->string('phone', 20);
            $table->string('email');
            $table->integer('capacity')->unsigned();
            $table->json('opening_hours')->nullable(); // Format: {"monday": {"open": "08:00", "close": "18:00"}, ...}
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreignId('director_id')->constrained('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daycares');
    }
};
