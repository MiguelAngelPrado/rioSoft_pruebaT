<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class QueryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = [
            1 => [
                'name' => 'siniestro_vehiculo_salida',
                'guard_name' => 'web',
                'alias' => 'Salida de Vehiculos'
            ],
            2 => [
                'name' => 'siniestro_vehiculo_recepcion',
                'guard_name' => 'web',
                'alias' => 'Recepcion de Vehiculos'
            ],
            3 => [
                'name' => 'siniestro_reporte_depositos',
                'guard_name' => 'web',
                'alias' => 'Reporte de stock de vehiculos por deposito'
            ]
        ];
        foreach($data as $key => $item){
            echo $key.'<br>';
            DB::table('permissions')
                    ->insert([
                        'name' => $item['name'],
                        'guard_name' => $item['guard_name'],
                        'alias' => $item['alias']
                    ]);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
