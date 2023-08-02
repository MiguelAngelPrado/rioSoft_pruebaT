<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Taller;
class TallerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        for($i=1; $i < 30; $i++){
            Taller::create([
                'nombre'=>'Taller '.$i
            ]);
        }
    }
}
