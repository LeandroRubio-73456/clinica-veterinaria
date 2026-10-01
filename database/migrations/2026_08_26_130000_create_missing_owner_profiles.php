<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement(<<<'SQL'
            INSERT INTO owners (user_id, first_name, last_name, email, phone, address, created_at, updated_at)
            SELECT users.id,
                   SUBSTR(users.name, 1, 50),
                   'Pendiente',
                   users.email,
                   'Pendiente',
                   'Pendiente de actualizar',
                   CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
            FROM users
            INNER JOIN roles ON roles.id = users.role_id AND roles.slug = 'propietario'
            LEFT JOIN owners ON owners.user_id = users.id OR owners.email = users.email
            WHERE owners.id IS NULL
        SQL);
    }

    public function down(): void
    {
        // No se eliminan perfiles creados durante la reparación automática.
    }
};
