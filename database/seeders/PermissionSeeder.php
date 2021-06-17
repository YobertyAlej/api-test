<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $create_user = Permission::firstOrCreate([
            'name' => 'create_user',
            'label' => 'Crear usuario'
        ]);

        $list_users = Permission::firstOrCreate([
            'name' => 'list_users',
            'label' => 'Listar usuarios'
        ]);

        $edit_user = Permission::firstOrCreate([
            'name' => 'edit_user',
            'label' => 'Editar usuario'
        ]);

        $delete_user = Permission::firstOrCreate([
            'name' => 'delete_user',
            'label' => 'Eliminar usuario'
        ]);

        $show_user = Permission::firstOrCreate([
            'name' => 'show_user',
            'label' => 'Ver detalle de usuario'
        ]);

        $manage_permission = Permission::firstOrCreate([
            'name' => 'manage_permissions',
            'label' => 'Manejar permisos'
        ]);
    }
}
