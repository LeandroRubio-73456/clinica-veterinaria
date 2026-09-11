<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username', 30)->nullable()->unique()->after('name');
        });

        Schema::table('owners', function (Blueprint $table) {
            $table->string('cedula', 20)->nullable()->unique()->after('user_id');
        });

        Schema::table('veterinarians', function (Blueprint $table) {
            $table->string('cedula', 20)->nullable()->unique()->after('user_id');
        });
    }

    public function down(): void
    {
        Schema::table('veterinarians', function (Blueprint $table) {
            $table->dropColumn('cedula');
        });

        Schema::table('owners', function (Blueprint $table) {
            $table->dropColumn('cedula');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['username']);
            $table->dropColumn('username');
        });
    }
};
