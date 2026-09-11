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
        Schema::create('surgeries', function (Blueprint $table) {
            $table->id();
            $table->date('scheduled_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->time('actual_start_time')->nullable();
            $table->time('actual_end_time')->nullable();
            $table->enum('state', ['scheduled','in_progress','completed','cancelled','no_show'])->default('scheduled');
            $table->text('notes')->nullable();
            $table->foreignId('pet_id')->constrained()->onDelete('restrict');
            $table->foreignId('veterinarian_id')->constrained()->onDelete('restrict');
            $table->foreignId('operating_room_id')->constrained()->onDelete('restrict');
            $table->foreignId('surgery_type_id')->constrained()->onDelete('restrict');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surgeries');
    }
};
