<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TipoDoc;

class TipoDocSeeder extends Seeder
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
                'codigo'=>'DNI',
                'nombre'=>'documento nacional de identidad',
            ],
            1 => [
                'codigo'=>'CE',
                'nombre'=>'carnet de extranjeria',
            ],
            2 => [
                'codigo'=>'NIT',
                'nombre'=>'Codigo NIT',
            ],
        ];

        foreach($data as $item){
            TipoDoc::create([
                'codigo'=>$item['codigo'],
                'nombre'=>$item['nombre']
            ]);
        }
    }
}
