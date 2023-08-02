<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UsuariosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $usuarios = [
            0 => [
                'name' => 'administrador',
                'email' => 'admin@gmail.com',
                'password' => 'Per5+2022',
                'status' => 'activo',
                'Rol' => 'administrador',
                'persona' => null,
            ],
            1 => [
                'name' => 'tramitador',
                'email' => 'tramitador@gmail.com',
                'password' => '12345678',
                'status' => 'activo',
                'Rol' => 'tramitador',
                'persona' => null,
            ],
            2 => [
                'name' => 'notaria',
                'email' => 'notaria@gmail.com',
                'password' => '12345678',
                'status' => 'activo',
                'Rol' => 'notaria',
                'persona' => null,
            ],
            3 => [
                'name' => 'gruero',
                'email' => 'gruero@gmail.com',
                'password' => '12345678',
                'status' => 'activo',
                'Rol' => 'gruero',
                'persona' => null,
            ],
        ];

        foreach ($usuarios as $item) {
           $user = User::create([
                'name' => $item['name'],
                'email' => $item['email'],
                'password' => Hash::make($item['password'])
           ]); 
           
           $user->assignRole($item['Rol']);
        }
    }
}
