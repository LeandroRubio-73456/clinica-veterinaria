<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement(<<<'SQL'
            UPDATE owners
            SET user_id = (
                SELECT users.id FROM users
                WHERE users.email = owners.email
                  AND users.role_id = (SELECT id FROM roles WHERE slug = 'propietario' LIMIT 1)
                LIMIT 1
            )
            WHERE user_id IS NULL
              AND EXISTS (
                SELECT 1 FROM users
                WHERE users.email = owners.email
                  AND users.role_id = (SELECT id FROM roles WHERE slug = 'propietario' LIMIT 1)
              )
        SQL);
    }

    public function down(): void
    {
        // La vinculación recuperada no debe eliminarse al revertir la migración.
    }
};
