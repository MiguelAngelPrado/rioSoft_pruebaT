<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Modelo;
use App\Models\Marca;
use Alert;
use DB;

class ModeloController extends Controller
{
   
   public function getMarca(){
    return Marca::pluck('nombre','idmarca');
   }

    public function index()
    {
        $data = Modelo::orderby('updated_at','desc')->get();
        return view('pages.modelo.lista',[
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
        return view('pages.modelo.form',[
            'marcas'=>$this->getMarca()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();

        Validator::make($input, [
            'nombre' => ['required', 'string', 'max:100'],
            'marca' => ['required', 'integer'],
            'estado' => ['required', 'integer'],
        ])->validate();

        Modelo::create([
            'nombre'=>$input['nombre'],
            'idmarca'=>$input['marca'],
            'condicion'=>$input['estado'] == 2 ? 0 : 1,
            'created_by'=>auth()->user()->id,
            'last_updated_by'=>auth()->user()->id,
        ]);

        Alert::toast('Modelo registrado correctamente.','success');
        return redirect()->route('modelo.index');
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
        $edit = Modelo::where('idmodelo',$id)->first();

        return view('pages.modelo.form',[
            'marcas'=>$this->getMarca(),
            'edit'=>$edit
        ]);
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
        $input = $request->all();
        
        $data = Modelo::where('idmodelo','=',$id)->first();
        $data->nombre = $input['nombre'];
        $data->idmarca = $input['marca'];
        $data->condicion = $input['estado'] == 2 ? 0 : 1;
        $data->last_updated_by = auth()->user()->id;
        $data->save();

        Alert::toast('Modelo actuaizado correctamente.','success');
        return redirect()->route('modelo.index');  
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function create_permiso()
    {
        //
        DB::table('permissions')->insert([
            'name'=>'siniestro_formulario_documentos1',
            'guard_name'=>'web',
            'alias'=>'Modulo Documentos',
        ]);
    }
}
