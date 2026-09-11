<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_system')->default(false);
            $table->timestamps();
        });

        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('permission_role', function (Blueprint $table) {
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->foreignId('permission_id')->constrained()->cascadeOnDelete();
            $table->primary(['role_id', 'permission_id']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->nullable()->after('role')->constrained('roles')->nullOnDelete();
        });

        Schema::table('owners', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->unique()->after('id')->constrained('users')->nullOnDelete();
        });

        $roles = [
            ['name' => 'Administrador', 'slug' => 'admin', 'description' => 'Acceso completo al sistema.', 'is_system' => true],
            ['name' => 'Administrativo', 'slug' => 'administrativo', 'description' => 'Gestiona agenda, propietarios y mascotas.', 'is_system' => true],
            ['name' => 'Veterinario', 'slug' => 'veterinario', 'description' => 'Consulta y opera cirugías asignadas.', 'is_system' => true],
            ['name' => 'Propietario', 'slug' => 'propietario', 'description' => 'Consulta sus mascotas y recordatorios.', 'is_system' => true],
        ];
        foreach ($roles as $role) {
            DB::table('roles')->insert(array_merge($role, ['created_at' => now(), 'updated_at' => now()]));
        }

        $permissions = [
            ['name' => 'Gestionar usuarios', 'slug' => 'users.manage', 'description' => 'Crear, editar y eliminar cuentas internas.'],
            ['name' => 'Gestionar roles', 'slug' => 'roles.manage', 'description' => 'Crear perfiles y asignar permisos.'],
            ['name' => 'Gestionar agenda', 'slug' => 'surgeries.manage', 'description' => 'Programar y actualizar cirugías.'],
            ['name' => 'Consultar reportes', 'slug' => 'reports.view', 'description' => 'Consultar indicadores operativos.'],
        ];
        foreach ($permissions as $permission) {
            DB::table('permissions')->insert(array_merge($permission, ['created_at' => now(), 'updated_at' => now()]));
        }

        $roleIds = DB::table('roles')->pluck('id', 'slug');
        DB::table('users')->whereIn('role', $roleIds->keys())->update([
            'role_id' => DB::raw("CASE role WHEN 'admin' THEN {$roleIds['admin']} WHEN 'administrativo' THEN {$roleIds['administrativo']} WHEN 'veterinario' THEN {$roleIds['veterinario']} END"),
        ]);

        $adminId = $roleIds['admin'];
        DB::table('permission_role')->insert(
            DB::table('permissions')->pluck('id')->map(fn ($id) => ['role_id' => $adminId, 'permission_id' => $id])->all()
        );
    }

    public function down(): void
    {
        Schema::table('owners', fn (Blueprint $table) => $table->dropForeign(['user_id']));
        Schema::table('owners', fn (Blueprint $table) => $table->dropColumn('user_id'));
        Schema::table('users', fn (Blueprint $table) => $table->dropForeign(['role_id']));
        Schema::table('users', fn (Blueprint $table) => $table->dropColumn('role_id'));
        Schema::dropIfExists('permission_role');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('roles');
    }
};
