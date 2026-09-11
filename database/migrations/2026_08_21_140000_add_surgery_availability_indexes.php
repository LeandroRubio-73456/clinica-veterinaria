<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('surgeries', function (Blueprint $table) {
            $table->index('scheduled_date', 'surgeries_scheduled_date_index');
            $table->index('state', 'surgeries_state_index');
            $table->index('veterinarian_id', 'surgeries_veterinarian_id_index');
            $table->index('operating_room_id', 'surgeries_operating_room_id_index');
        });
    }

    public function down(): void
    {
        Schema::table('surgeries', function (Blueprint $table) {
            $table->dropIndex('surgeries_scheduled_date_index');
            $table->dropIndex('surgeries_state_index');
            $table->dropIndex('surgeries_veterinarian_id_index');
            $table->dropIndex('surgeries_operating_room_id_index');
        });
    }
};
