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
        Schema::create('pets', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->string('breed', 100)->nullable();
            $table->unsignedTinyInteger('age');
            $table->decimal('weight', 5,2);
            $table->enum('gender', ['male', 'female']);
            $table->enum('state', ['active', 'inactive'])->default('active');
            $table->foreignId('species_id')->constrained()->onDelete('restrict');
            $table->foreignId('owner_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pets');
    }
};
