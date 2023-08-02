<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $permisos = [
            0 => ['name'=>'siniestro_listar', 'alias' => 'Listado de los Siniestros registrados'],
            1 => ['name'=>'siniestro_editar', 'alias' => 'Editar Siniestro registrado'],
            2 => ['name'=>'siniestro_documentos', 'alias' => 'Ver Documentación del Siniestro'],
            3 => ['name'=>'siniestro_acta_inicial', 'alias' => 'Subir Acta Inicial'],
            4 => ['name'=>'siniestro_acta_final', 'alias' => 'Subir Acta Fina'],
            5 => ['name'=>'siniestro_detalle_ver', 'alias' => 'Ver información'],
            6 => ['name'=>'siniestro_detalle_editar', 'alias' => 'Editar información'],
            7 => ['name'=>'siniestro_bitacora', 'alias' => 'Visualizar Bitacora'],
            8 => ['name'=>'siniestro_registrar', 'alias' => 'Registrar Siniestro'],
            9 => ['name'=>'siniestro_salida', 'alias' => 'Registrar Salida'],
            10 => ['name'=>'siniestro_fotos', 'alias' => 'Subir Fotos'],
            11 => ['name'=>'siniestro_generar_qr', 'alias' => 'Generar QR'],
            12 => ['name'=>'siniestro_correo', 'alias' => 'Configuración de correo'],
            13 => ['name'=>'roles_listar', 'alias' => 'Control de Roles'],
            14 => ['name'=>'usuarios_listar', 'alias' => 'Control de Usuarios'],
            15 => ['name'=>'siniestro_reporte_deposito', 'alias' => 'Reporte de Deposito'],
            16 => ['name'=>'siniestro_deposito', 'alias' => 'Gestion en datos de deposito'],
        ];
        foreach ($permisos as $item) {
            Permission::create([
                'name'=>$item['name'],
                'alias'=>$item['alias']
            ]);
        }
    }
}
