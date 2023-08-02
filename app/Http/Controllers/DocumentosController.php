<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Documentos;
use Alert;
use DB;

class DocumentosController extends Controller
{
 
    public function index()
    {
        $data = Documentos::all();
        
        return view('pages.documentos.index',[
            'data' => $data
        ]);   
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.documentos.form');
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
            'estado' => ['required', 'integer'],
        ])->validate();

        Documentos::create([
            'nombre'=>$input['nombre'],
            'estado'=>$input['estado'] == 2 ? 0 : 1,
        ]);

        Alert::toast('Documento registrado correctamente.','success');
        return redirect()->route('documento.index');
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
        $edit = Documentos::find($id);
        return view('pages.documentos.form',[
            'edit' => $edit
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
        $input = $request->all();
        
        $data = Documentos::find($id);
        $data->nombre = $input['nombre'];
        $data->estado = $input['estado'] == 2 ? 0 : 1;
        $data->save();

        Alert::toast('Documento actuaizado correctamente.','success');
        return redirect()->route('documento.index');  
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
