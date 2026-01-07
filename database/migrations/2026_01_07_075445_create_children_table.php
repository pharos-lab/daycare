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
        Schema::create('children', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daycare_id')->constrained()->cascadeOnDelete();
            $table->string('first_name');
            $table->string('last_name');
            $table->date('birth_date');
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->text('emergency_contact')->nullable();
            $table->date('enrollment_date')->nullable();
            $table->timestamps();
        });

        // Pivot table for children and parents relationship
        Schema::create('child_parent', function (Blueprint $table) {
            $table->id();
            $table->foreignId('child_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_id')->constrained('users')->cascadeOnDelete();
            $table->enum('relationship', ['mother', 'father', 'guardian', 'other'])->default('other');
            $table->timestamps();

            $table->unique(['child_id', 'parent_id']);
            $table->index('child_id');
            $table->index('parent_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('child_parent');
        Schema::dropIfExists('children');
    }
};
