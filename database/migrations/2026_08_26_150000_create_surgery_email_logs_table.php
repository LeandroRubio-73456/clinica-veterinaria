<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('surgery_email_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('surgery_id')->constrained()->cascadeOnDelete();
            $table->string('recipient_email');
            $table->string('notification_type', 40);
            $table->timestamp('sent_at');
            $table->timestamps();
            $table->unique(['surgery_id', 'recipient_email', 'notification_type'], 'surgery_email_log_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surgery_email_logs');
    }
};
