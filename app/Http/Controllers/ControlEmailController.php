<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ControlEmail;

class ControlEmailController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = ControlEmail::all();

        return view('pages.control_emails.index',[
            'data' => $data
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = $data = ControlEmail::find($id);
        $data->cliente = null;
        $data->vehiculo = null;
        
        $data_solicitud = $data->solicitud;

        if($data_solicitud != null){
            if($data_solicitud->persona->tipo == 'natural'){
                $data->cliente = $data_solicitud->persona->apellidos.' '.$data_solicitud->persona->nombres;
            }else{
                $data->cliente = $data_solicitud->persona->razon_social;
            }
            
            if($data_solicitud->vehiculo != null){
                $data->vehiculo = '<ul><li>Placa: '.$data_solicitud->vehiculo->placa.'</li><li>Marca: '.$data_solicitud->vehiculo->marca.'</li><li>Modelo: '.$data_solicitud->vehiculo->modelo.'</li></ul>';
            }
        }

        $data_correos = json_decode($data->correos);
        $items_correos = '';
        foreach($data_correos as $item){
            $items_correos .= $item.', ';
        }
        $data->correos = $items_correos;
        
        return json_encode($data);
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
