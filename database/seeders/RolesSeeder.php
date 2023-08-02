<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $roles = [
            0 => [
                'name'=>'administrador',
                'guard_name'=>'web',
                'permisos'=>[
                    'siniestro_listar',
                    'siniestro_editar',
                    'siniestro_documentos',
                    'siniestro_acta_inicial',
                    'siniestro_acta_final',
                    'siniestro_detalle_ver',
                    'siniestro_detalle_editar',
                    'siniestro_bitacora',
                    'siniestro_registrar',
                    'siniestro_salida',
                    'siniestro_fotos',
                    'siniestro_generar_qr',
                    'siniestro_correo',
                    'roles_listar',
                    'usuarios_listar',
                    'siniestro_reporte_deposito',
                    'siniestro_deposito'
                ]
            ],
            1 => [
                'name'=>'tramitador',
                'guard_name'=>'web',
                'permisos'=>[
                    'siniestro_detalle_ver',
                    'siniestro_acta_final',
                    'siniestro_bitacora',
                    'siniestro_documentos',
                    'siniestro_listar',
                    'siniestro_registrar',
                    'siniestro_editar',
                    'siniestro_fotos',
                ]
            ],
            2 => [
                'name'=>'notaria',
                'guard_name'=>'web',
                'permisos'=>[
                    'siniestro_documentos',
                    'siniestro_listar',
                    'siniestro_acta_inicial',
                ]
            ],
            3 => [
                'name'=>'gruero',
                'guard_name'=>'web',
                'permisos'=>[
                    'siniestro_listar',
                    'siniestro_detalle_editar',
                    'siniestro_generar_qr',
                    'siniestro_salida',
                    'siniestro_fotos',
                    'siniestro_reporte_deposito'
                    
                ]
            ]
        ];
        foreach ($roles as $item) {
            $rol = Role::create([
                'name'=>$item['name'],
                'guard_name'=>$item['guard_name'],
            ]);
            foreach($item['permisos'] as $permisos){
                $rol->givePermissionTo($permisos);
            }
        }
    }
}
