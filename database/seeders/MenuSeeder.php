<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;
use Spatie\Permission\Models\Permission;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $data = [
            0 => [
                'id_padre' => null,
                'id_permiso'=> null,
                'nombre'=>'inicio',
                'alias'=>'Inicio',
                'ruta'=>'/',
                'icono'=>'fas fa-tachometer-alt'
            ],
            1 => [
                'id_padre' => 1,
                'id_permiso'=>'siniestro_listar',
                'nombre'=>'siniestro',
                'alias'=>'Siniestros',
                'ruta'=>'siniestros/listar',
                'icono'=>'fas fa-table'
            ],
            2 => [
                'id_padre' => null,
                'id_permiso'=> null,
                'nombre'=>'catalogos',
                'alias'=>'Catálogos',
                'ruta'=>'/',
                'icono'=>'fas fa-copy'
            ],
            3 => [
                'id_padre' => 3,
                'id_permiso'=> 'notaria_listar',
                'nombre'=>'notarias',
                'alias'=>'Notarias',
                'ruta'=>'notarias/listar',
                'icono'=>'fas fa-table'
            ],
            4 => [
                'id_padre' => null,
                'id_permiso'=> 'tramitador_listar',
                'nombre'=>'tramitador',
                'alias'=>'Tramitados',
                'ruta'=>'tramitados/listar',
                'icono'=>'fas fa-book'
            ],
            5 => [
                'id_padre' => null,
                'id_permiso'=> null,
                'nombre'=>'niver_uno',
                'alias'=>'Nivel 1',
                'ruta'=>'',
                'icono'=>'fas fa-circle'
            ],
            6 => [
                'id_padre' => 6,
                'id_permiso'=> null,
                'nombre'=>'niver_dos',
                'alias'=>'Nivel 2',
                'ruta'=>'',
                'icono'=>'far fa-circle'
            ],
            7 => [
                'id_padre' => 6,
                'id_permiso'=> null,
                'nombre'=>'niver_dos',
                'alias'=>'Nivel 2',
                'ruta'=>'',
                'icono'=>'far fa-circle'
            ],
            8 => [
                'id_padre' => 8,
                'id_permiso'=> null,
                'nombre'=>'niver_tres',
                'alias'=>'Nivel 3',
                'ruta'=>'',
                'icono'=>'far fa-dot-circle'
            ],
            9 => [
                'id_padre' => 8,
                'id_permiso'=> null,
                'nombre'=>'niver_tres',
                'alias'=>'Nivel 3',
                'ruta'=>'',
                'icono'=>'far fa-dot-circle'
            ],
            10 => [
                'id_padre' => null,
                'id_permiso'=> null,
                'nombre'=>'sum_menu',
                'alias'=>'Sub Menu',
                'ruta'=>'',
                'icono'=>'fas fa-circle'
            ],
            11 => [
                'id_padre' => null,
                'id_permiso'=> null,
                'nombre'=>'sum_menu',
                'alias'=>'Sub Menu 2',
                'ruta'=>'',
                'icono'=>'fas fa-circle'
            ],
        ];
        foreach($data as $item){
            $id_permiso = Permission::where('name','=',$item['id_permiso'])->first();
            Menu::create([
                'id_padre'=>$item['id_padre'],
                'id_permiso'=>$id_permiso != null ? $id_permiso->id : null,
                'nombre'=>$item['nombre'],
                'alias'=>$item['alias'],
                'ruta'=>$item['ruta'],
                'icono'=>$item['icono']
            ]);
        }
    }
}
