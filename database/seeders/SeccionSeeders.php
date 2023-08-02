<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Seccion;

class SeccionSeeders extends Seeder
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
            0 => ['nombre'=>'Registrar Siniestro'],
            1 => ['nombre'=>'Asignación de Notaria'],
            2 => ['nombre'=>'Subir Acta Inicial'],
            3 => ['nombre'=>'Subir Acta Final']
        ];
        foreach($data as $item){
            Seccion::create([
                'nombre'=>$item['nombre']
            ]);
        }
    }
}
