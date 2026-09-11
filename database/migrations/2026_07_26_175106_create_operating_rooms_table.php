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
        Schema::create('operating_rooms', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('type', 100)->nullable();
            $table->enum('state', ['available','occupied','maintenance', 'inactive'])->default('available');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operating_rooms');
    }
};
