<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\Seccion;
use App\Models\Correo;
Use Alert;

class CorreoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Seccion::all();
        return view('pages.correo.configuracion',[
            'data'=>$data
        ]);
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
        $input = $request->all();
        $sec = Seccion::all();
        $data_val = [];
        $bandera = false;
        //dd($input);
        foreach($sec as $key => $item){
            $data_val['asunto_'.$item->id] = ['required'];
            $data_val['texto_'.$item->id] = ['required'];
        }
        
        Validator::make($input, $data_val)->validate();
        
            foreach($sec as $key => $item){
                if($input['id_correo_'.$item->id] != '0'){
                    $update = Correo::where('id_seccion',$item->id)->first();
                    $update->asunto = $input['asunto_'.$item->id];
                    $update->texto = $input['texto_'.$item->id];
                    $update->correos = json_encode($input['correos'.$item->id]);
                    $update->save();
                    $bandera = true;
                }else{
                    Correo::create([
                        'asunto'=>$input['asunto_'.$item->id],
                        'texto'=>$input['texto_'.$item->id],
                        'id_seccion'=>$item->id,
                        'correos'=>json_encode($input['correos'.$item->id])
                    ]);    
                    $bandera = false;
                }
            }
            if($bandera){
                Alert::toast('Configuración actualizada correctamente.','success');
                return redirect()->route('correo.index');
            }else{
                Alert::toast('Configuración guardada correctamente.','success');
                return redirect()->route('correo.index');    
            }
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
