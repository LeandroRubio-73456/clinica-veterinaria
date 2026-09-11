<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scheduling_conflicts', function (Blueprint $table) {
            $table->id();
            $table->date('scheduled_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('conflict_type', 40);
            $table->string('source', 20)->default('application');
            $table->foreignId('veterinarian_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('operating_room_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('surgery_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->json('details')->nullable();
            $table->timestamps();

            $table->index(['scheduled_date', 'conflict_type']);
            $table->index(['veterinarian_id', 'scheduled_date']);
            $table->index(['operating_room_id', 'scheduled_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scheduling_conflicts');
    }
};
