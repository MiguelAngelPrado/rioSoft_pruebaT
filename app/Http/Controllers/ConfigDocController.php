<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Mail\SolicitudDoc;
use App\Models\Solicitud;
use App\Models\Documentos;
use App\Models\ConfDoc;
use App\Models\ConfigEmail;
use App\Models\Expediente;
use App\Models\Historico;
Use Alert;
use Mail;
use DB;

class ConfigDocController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        $data_emails = '';
        
        $data = Solicitud::where('id_solicitud',$id)->first();
        
        $documentos = Documentos::where('estado',1)->get();

        $conf_doc = ConfDoc::where('id_solicitud','=',$data->id)->pluck('id_documento')->toArray();
        
        $data_conf_email = ConfigEmail::where('id_solicitud','=',$data->id)->first();
        
        if($data_conf_email != null){
            
            foreach(json_decode($data_conf_email->emails) as $item){
                if($item != ''){
                 $data_emails .= $item.',';       
                }
            }
            
        }else{
            $data_emails = $data->persona->correo;
        }

        return view('pages.config_doc.index',[
            'edit' => $data,
            'documentos' => $documentos,
            'conf_doc' => $conf_doc,
            'data_emails' => $data_emails
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function send(Request $request, $id){
        $input = $request->all();
        
        $control  = $input['control'];
        $documento = DB::table('documentos')
                        ->select('documentos.nombre','documentos.id')
                        ->join('conf_doc','conf_doc.id_documento','=','documentos.id')
                        ->where('conf_doc.id','=',$input['id_documento_'.$control])
                        ->first();
        
        
        Validator::make($input, [
            'documento_'.$control =>['mimes:pdf']
        ], $messages = [
            'mimes' => 'El documento '.$documento->nombre.' debe ser un archivo de tipo: :values.' 
        ])->validate();
            
            $solicitud = Solicitud::where('id_solicitud','=',$id)->first();

            $mime = $request->file('documento_'.$control)->getMimeType();
            $extension = strtolower($request->file('documento_'.$control)->getClientOriginalExtension());
            $name = $id.'_'.'documento'.date("Y-m-d_H_i_s").'.'.$extension;
            $path = base_path() . '/public/uploads/documentos/';
            $request->file('documento_'.$control)->move($path, $name);

            $expediente = Expediente::where('id_solicitud',$solicitud->id)->first();

            $new_fiel = new Expediente();
            $new_fiel->id_solicitud = $solicitud->id;
            $new_fiel->id_documento = $documento->id;
            $new_fiel->ruta = $name;
            $new_fiel->tipo = 'pdf';
            $new_fiel->comentario = 'Documento cargado por el cliente ('.date('Y-m-d H:i:s').')';
            $new_fiel->save();

            $historico = new Historico();
            $historico->id_solicitud = $solicitud->id;
            $historico->estado = 'carga_documentos';
            $historico->documento = true;
            $historico->id_expediente = $new_fiel->id;
            $historico->save();

            Alert::toast('Documento cargado correctamente.','success');
            return redirect()->route('subir.documentos',$id);
    }

    public function edit($id)
    {
        $data = Solicitud::where('id_solicitud','=',$id)->first();
        $documentos = null;
        
        if($data != null){
            $documentos = ConfDoc::select('documentos.nombre','conf_doc.id','expediente.id as id_expediente')
                                ->join('documentos','documentos.id','=','conf_doc.id_documento')
                                ->leftjoin('expediente',function($q) use ($data){
                                    $q->on('expediente.id_documento','=','conf_doc.id_documento')
                                    ->where('expediente.id_solicitud','=',$data->id);
                                })
                                ->where('conf_doc.id_solicitud','=',$data->id)
                                //->wherenull('expediente.id')
                                ->get();    
        }

        return view('pages.config_doc.form_documentos',[
            'data' => $data,
            'documentos' => $documentos
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
        
        $id_solicitud = Solicitud::where('id_solicitud','=',$id)->first();
        /*---02-07-2023 Configuraciond de correos---------------------------------------------------*/
        $conf_email = ConfigEmail::where('id_solicitud','=',$id_solicitud->id)->first();
        
        $emails_row = json_encode(explode(',',$input['h_email']));

        if($conf_email != null){
            $conf_email->emails = $emails_row;
            $conf_email->save();
        }else{
            $conf_email = new ConfigEmail();
            $conf_email->emails = $emails_row;
            $conf_email->id_solicitud = $id_solicitud->id;
            $conf_email->save();
        }
        /*-------------------------------*/

        ConfDoc::where('id_solicitud','=',$id_solicitud->id)->delete();

        $aux_save = 0;
        foreach($input['documento_data'] as $item){
            $save_doc = new ConfDoc();
            $save_doc->id_solicitud = $id_solicitud->id;
            $save_doc->id_documento = $item;
            $save_doc->id_usuario = auth()->user()->id;
            $save_doc->save();
            $aux_save++;
        }

        $cliente = $id_solicitud->persona->nombres.' '.$id_solicitud->persona->apellidos;
        $numero_siniestro = $id_solicitud->numero;
        $placa = $id_solicitud->vehiculo->placa;
        $tramitador = $id_solicitud->tramitador->name;

        $correos = str_replace('"','',str_replace('"]','',str_replace('["','',DB::table('correo')->where('id','=',1)->pluck('correos')->toArray()[0])));
        $send_correos =  explode(',',$input['h_email'].$correos);
        //dd($send_correos);
        //$correo_send = $id_solicitud->persona->correo;
        //array_push($send_correos,$correo_send);
        
        if($aux_save != 0){
            $documentos = ConfDoc::select('documentos.nombre')
                                ->join('documentos','documentos.id','=','conf_doc.id_documento')
                                ->leftjoin('expediente',function($q) use ($id_solicitud){
                                    $q->on('expediente.id_documento','=','conf_doc.id_documento')
                                    ->where('expediente.id_solicitud','=',$id_solicitud->id);
                                })
                                ->where('conf_doc.id_solicitud','=',$id_solicitud->id)
                                ->wherenull('expediente.id')
                                ->get()
                                ->toArray();

            if(count($documentos) > 0){
                Mail::to($send_correos)->send(new SolicitudDoc($cliente, $numero_siniestro, $placa, $tramitador, $documentos,$id, 'Solicitud de documentación requerida '.date('d/m/Y'),'infosiniestros@esiscad.com'));

                $data_documents = '';
                foreach($documentos as $key => $item_doc){
                    $data_documents .= ($key+1).'.-'.$item_doc['nombre'].',';
                }

                DB::table('emails')->insert([
                    'id_solicitud' => $id_solicitud->id,
                    'correos' => json_encode($send_correos),
                    'tipo' => 'notificacion_documentos',
                    'asunto' => 'Solicitud de documentación requerida'.date('d/m/Y'),
                    'texto' => 'Envio de notificacion de los siguientes documentos. '.$data_documents,
                    'created_at' => date('Y-m-d H:i:'),
                    'updated_at' => date('Y-m-d H:i:')
                ]);
            }
            
        }

        

        Alert::toast('Condiguración cargada correctamente.','success');
        return redirect()->route('siniestro.index');
    }

        public function tarea_envio(){
        $data_config = ConfDoc::select('id_solicitud')->distinct()->get();

        $correos = str_replace('"','',str_replace('"]','',str_replace('["','',DB::table('correo')->where('id','=',1)->pluck('correos')->toArray()[0])));
        

        foreach($data_config as $item){

            $send_correos =  explode(',',$correos);
            $solicitud = Solicitud::where('id','=',$item->id_solicitud)->first();         
            
            $documentos = ConfDoc::select('documentos.nombre')
                                ->join('documentos','documentos.id','=','conf_doc.id_documento')
                                ->leftjoin('expediente',function($q) use ($item){
                                    $q->on('expediente.id_documento','=','conf_doc.id_documento')
                                    ->where('expediente.id_solicitud','=',$item->id_solicitud);
                                })
                                ->where('conf_doc.id_solicitud','=',$item->id_solicitud)
                                ->wherenull('expediente.id')
                                ->get()
                                ->toArray();

            $cliente = $solicitud->persona->nombres.' '.$solicitud->persona->apellidos;
            $numero_siniestro = $solicitud->numero;
            $placa = $solicitud->vehiculo->placa;
            $tramitador = $solicitud->tramitador->name;
            $correo_send = $solicitud->persona->correo;
            
            array_push($send_correos,$correo_send);
            
            if(count($documentos) > 0){
//$send_correos
                Mail::to('iti.miguel.prado@gmail.com')->send(new SolicitudDoc($cliente, $numero_siniestro, $placa, $tramitador, $documentos,$solicitud->id_solicitud, 'Solicitud de documentación requerida '.date('d/m/Y'),'infosiniestros@esiscad.com'));

                $data_documents = '';
                foreach($documentos as $key => $item_doc){
                    $data_documents .= ($key+1).'.-'.$item_doc['nombre'].',';
                }

                DB::table('emails')->insert([
                    'id_solicitud' => $item->id_solicitud,
                    'correos' => json_encode($send_correos),
                    'tipo' => 'notificacion_documentos',
                    'asunto' => 'Solicitud de documentación requerida'.date('d/m/Y'),
                    'texto' => 'Envio de notificacion de los siguientes documentos. '.$data_documents,
                    'created_at' => date('Y-m-d H:i:'),
                    'updated_at' => date('Y-m-d H:i:')
                ]);
            }

        }
    }
    public function query(){
        $query_1 = DB::table('solicitud')->where('id_solicitud','=','')->first();
        DB::table('expediente')->where('id_solicitud','=',1)->first();
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
   
}
