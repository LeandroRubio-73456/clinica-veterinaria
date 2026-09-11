<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement(<<<'SQL'
            UPDATE owners
            INNER JOIN users ON users.email = owners.email
            SET owners.user_id = users.id
            WHERE owners.user_id IS NULL
              AND users.role_id = (SELECT id FROM roles WHERE slug = 'propietario' LIMIT 1)
        SQL);
    }

    public function down(): void
    {
        // La vinculación recuperada no debe eliminarse al revertir la migración.
    }
};
