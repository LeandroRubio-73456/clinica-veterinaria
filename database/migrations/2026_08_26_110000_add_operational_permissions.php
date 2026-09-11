<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        foreach ([
            ['name' => 'Gestionar catálogos', 'slug' => 'catalogs.manage'],
            ['name' => 'Gestionar propietarios y mascotas', 'slug' => 'people.manage'],
            ['name' => 'Consultar agenda', 'slug' => 'surgeries.view'],
            ['name' => 'Operar cirugías', 'slug' => 'surgeries.operate'],
        ] as $permission) {
            DB::table('permissions')->updateOrInsert(['slug' => $permission['slug']], [...$permission, 'created_at' => now(), 'updated_at' => now()]);
        }
    }

    public function down(): void
    {
        DB::table('permissions')->whereIn('slug', ['catalogs.manage', 'people.manage', 'surgeries.view', 'surgeries.operate'])->delete();
    }
};
