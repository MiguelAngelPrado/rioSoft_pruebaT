<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Notaria;
use App\Models\User;
use App\Models\Persona;

class NotariaSeeder extends Seeder
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
                    'nombre'=>'CORCUERA GARCIA MARCO ANTONIO',
                    'provincia'=>'TRUJILLO',
                    'telefono'=>'044-235263 ANEXO 20',
                    'address'=>'JR ORBEGOZO 289/312 - TRUJILLO',
                    'user'=> [
                        'password'=>'123456789'
                    ],
                    'persona'=>[
                        0=>[
                            'nombres'=>'LUCIA',
                            'apellidos'=>'DE LA CRUZ',
                            'correo'=>'vehicular.notariacorcuera@hotmail.com'
                        ]
                    ]
                ],
            1 => [
                    'nombre'=>'QUINDE RAZURI',
                    'provincia'=>'PIURA',
                    'telefono'=>'073-305484 ANEXO 217',
                    'address'=>'JR CALLAO 343 - PIURA',
                    'user'=> [
                        'password'=>'123456789'
                    ],
                    'persona'=>[
                        0 => [
                        'nombres'=>'YADIRA',
                        'apellidos'=>'MONCADA',
                        'correo'=>'yadiramh0697@gmail.com'
                        ],
                        1 => [
                        'nombres'=>'YADIRA',
                        'apellidos'=>'MONCADA',
                        'correo'=>'DGARCIA@notariaquinde.com'
                        ]
                    ]
                ],
            2 => [
                    'nombre'=>'NOTARIA CARDENAS',
                    'provincia'=>'CHICLAYO',
                    'telefono'=>'73 -305484 ANEXO 217',
                    'address'=>'AV. SAENZ PE?A 2311 - CHICLAYO',
                    'user'=> [
                        'password'=>'123456789'
                    ],
                    'persona'=>[
                        0=>[
                            'nombres'=>'TAPIA',
                            'apellidos'=>'DRA. LUZ',
                            'correo'=>'ltapia@notaria-cardenas.com'
                        ]
                    ]
                ],
             3 => [
                    'nombre'=>'RODRIGUEZ VELARDE',
                    'provincia'=>'AREQUIPA',
                    'telefono'=>'255559 ANEXO 18',
                    'address'=>'URB. SE?ORIAL A-3 CAYMA - AREQUIPA',
                    'user'=> [
                        'password'=>'123456789'
                    ],
                    'persona'=>[
                        0=>[
                            'nombres'=>'XIMENA MIRANDA',
                            'apellidos'=>'MANRIQUE',
                            'correo'=>'ximena@rodriguezvelarde.com.pe'
                        ]
                    ]
                ],
            4 => [
                    'nombre'=>'FLAMIDIO VIGO SALDAÑA',
                    'provincia'=>'CAJAMARCA',
                    'telefono'=>'76 -312862',
                    'address'=>'JR APURIMAC 583 - CAJAMARCA',
                    'user'=> [
                        'password'=>'123456789'
                    ],
                    'persona'=>[
                        0=>[
                            'nombres'=>'FLAMINIO',
                            'apellidos'=>'VIGO',
                            'correo'=>'notariavigosaldana@gmail.com'
                        ]
                    ]
                ],
            5 => [
                    'nombre'=>'NOTARIO OCAMPO D LAHAZA',
                    'provincia'=>'CUSCO',
                    'telefono'=>'973201491',
                    'address'=>'AV. EL SOL 616 INT 5 - PASAJE GRACE -CUSCO',
                    'user'=> [
                        'password'=>'123456789'
                    ],
                    'persona'=>[
                        0=>[
                            'nombres'=>'CARMELA',
                            'apellidos'=>'PEÑA',
                            'correo'=>'ocampodelahaza@gmail.com'
                        ]
                    ]
                ],
            6 => [
                    'nombre'=>'NOTARIO RONAD VENERO',
                    'provincia'=>'HUANCAYO',
                    'telefono'=>'964697353/928222128',
                    'address'=>'JR. MOQUEGUA 206 - HUANCAYO',
                    'user'=> [
                        'password'=>'123456789'
                    ],
                    'persona'=>[
                        0=>[
                            'nombres'=>'RONAD',
                            'apellidos'=>'VENERO',
                            'correo'=>'notarioronaldvb@gmail.com'
                        ],
                        1=>[
                            'nombres'=>'RONAD',
                            'apellidos'=>'VENERO',
                            'correo'=>'transferenciasvehiculares@notariavenero.com.pe'
                        ]
                    ]
                ],
            7 => [
                    'nombre'=>'ANGUIS SAYERS DE ADAWI',
                    'provincia'=>'TACNA',
                    'telefono'=>'967735585',
                    'address'=>'CALLE VICENTE DAGNINO 324 - TACNA',
                    'user'=> [
                        'password'=>'123456789'
                    ],
                    'persona'=>[
                        0=>[
                            'nombres'=>'WALDO',
                            'apellidos'=>'OVIEDO ANGUIS',
                            'correo'=>'waldo_oviedo@notaria-anguis.com'
                        ],
                        1=>[
                            'nombres'=>'WALDO',
                            'apellidos'=>'OVIEDO ANGUIS',
                            'correo'=>'wroa1970@gmail.com'
                        ]
                    ]
                ],
            8 => [
                    'nombre'=>'FERNANDINI BARREDA',
                    'provincia'=>'LIMA',
                    'telefono'=>'934041766',
                    'address'=>'AV. PASEO DE LA REPUBLICA 3046 - SAN ISIDRO',
                    'user'=> [
                        'password'=>'123456789'
                    ],
                    'persona'=>[
                        0=>[
                            'nombres'=>'ZUMIKO',
                            'apellidos'=>'RODRIGUEZ',
                            'correo'=>'zrodriguez@notariafernandini.com'
                        ]
                    ]
                ],
            9 => [
                    'nombre'=>'MOREYRA PELOSI',
                    'provincia'=>'LIMA',
                    'telefono'=>'981420917',
                    'address'=>'AV. UNIVERSITARIA 900 - SAN MIGUEL',
                    'user'=> [
                        'password'=>'123456789'
                    ],
                    'persona'=>[
                        0=>[
                            'nombres'=>'CARLOS',
                            'apellidos'=>'ISLA',
                            'correo'=>'carlosisla@notariamoreyra.pe'
                        ]
                    ]
                ]
        ];

        foreach ($data as $item) {  
            $notaria = Notaria::create([
                'nombre'=>$item['nombre'],
                'provincia'=>$item['provincia'],
                'telefono'=>$item['telefono'],
                'address'=>$item['address'],
            ]);
            foreach($item['persona'] as $row){
                $user = User::create([
                    'name'=>$row['nombres'].' '.$row['apellidos'],
                    'email'=>$row['correo'],
                    'password'=>Hash::make($item['user']['password'])
                ]);

                $user->assignRole('notaria');

                Persona::create([
                    'nombres'=>$row['nombres'],
                    'apellidos'=>$row['apellidos'],
                    'correo'=>$row['correo'],
                    'telefono'=>$item['telefono'],
                    'id_usaurio'=>$user->id,
                    'id_notaria'=>$notaria->id
                ]);
            }
           

        }
    }
}
