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
            INNER JOIN roles ON roles.id = users.role_id AND roles.slug = 'propietario'
            SET owners.user_id = users.id
            WHERE owners.user_id IS NULL
        SQL);
    }

    public function down(): void
    {
        // La reparación de vínculos se conserva.
    }
};
