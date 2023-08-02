<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Documentos;
class DocumentosSeedeers extends Seeder
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
            0 => ['nombre' => 'DENUNCIA POLICIAL'],
            1 => ['nombre' => 'GRAVAMEN'],
            2 => ['nombre' => 'CARTA NO ADEUDO'],
            3 => ['nombre' => 'IMPUESTOS'],
            4 => ['nombre' => 'PAPELETAS'],
            5 => ['nombre' => 'SUTRAN'],
        ];
        foreach($data as $item){
            Documentos::create([
                'nombre' => $item['nombre']
            ]);
        }
    }
}
