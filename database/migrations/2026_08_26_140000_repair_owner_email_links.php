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
                INNER JOIN roles ON roles.id = users.role_id AND roles.slug = 'propietario'
                WHERE users.email = owners.email
                LIMIT 1
            )
            WHERE user_id IS NULL
              AND EXISTS (
                SELECT 1 FROM users
                INNER JOIN roles ON roles.id = users.role_id AND roles.slug = 'propietario'
                WHERE users.email = owners.email
              )
        SQL);
    }

    public function down(): void
    {
        // La reparación de vínculos se conserva.
    }
};
