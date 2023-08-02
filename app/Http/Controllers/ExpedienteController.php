<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Solicitud;
use App\Models\Expediente;
use ZipArchive;
Use Alert;

class ExpedienteController extends Controller
{
    public function generar_zip($id){
        $data = Solicitud::where('id_solicitud',$id)->first();
        $carpeta = 'documentos_'.$id.'.zip';

        $zip = new ZipArchive();
        $dir = public_path().'/uploads/zip';
        $zip->open($dir.'/'.$carpeta,ZipArchive::CREATE);
        
        if($data->boleta_informativa != null){
            $boleta_info = public_path().'/uploads/boletas_informativas/'.$data->boleta_informativa;    
            $zip->addFile($boleta_info,"boleta_informativa.pdf");
        }

        if($data->carta != null){
            $carta = public_path().'/uploads/carta/'.$data->carta;    
            $zip->addFile($carta,"carta_perdida_total.pdf");
        }

        if($data->acta_inicial != null){
            $acta_inicial = public_path().'/uploads/carta/'.$data->carta;    
            $zip->addFile($acta_inicial,"acta_inicial.pdf");
        }

        if($data->acta_final != null){
            $acta_final = public_path().'/uploads/carta/'.$data->carta;    
            $zip->addFile($acta_final,"acta_final.pdf");
        }

        foreach($data->expediente as $key => $doc){
            $name = strtolower(str_replace(' ','_',($doc->documento != null ? $doc->documento->nombre : 'OTRO_DOCUMENTO_'.($key+1)))).'.pdf';
            $file = public_path().'/uploads/documentos/'.$doc->ruta;
            $zip->addFile($file,$name);
        }

        $zip->close();
    }   
    public function documentos($id){
        $data = Solicitud::where('id_solicitud',$id)->first();
        //dd($data->expediente);
        //$this->generar_zip($id);
        return view('pages.expediente.documentos',[
            'data'=>$data
        ]);
    }
    public function cambio_estado_doc(Request $request)
    {
        $input = $request->all();
        $data_exp = Expediente::find($input['id']);
        $data_exp->estado = $input['estado'];
        $data_exp->save();
        return json_encode(true);
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
