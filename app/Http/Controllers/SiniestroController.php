<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use App\Mail\Siniestro;
use App\Models\Documentos;
use App\Models\Persona;
use App\Models\Vehiculo;
use App\Models\Solicitud;
use App\Models\Expediente;
use App\Models\Notaria;
use App\Models\TipoDoc;
use App\Models\Marca;
use App\Models\Modelo;
use App\Models\Deposito;
use App\Models\Ubicacion;
use App\Models\Correo;
use App\Models\Historico;
use App\Models\Taller;
use App\Models\Recuperacion;
use App\Models\Placa;
Use Alert;
use DB;
use PDF;
use Mail;

class SiniestroController extends Controller
{
    public function getAsegurado(Request $request){
        $input = $request->all();
        if (isset($input['search'])){
            $keyword = strtolower($input['search']);
            $prm =str_replace(' ','%',$keyword);

            try{

                $asegurado = DB::table('solicitud')
                                ->select('solicitud.id','solicitud.numero','persona.razon_social','persona.nombres','persona.apellidos')
                                ->join('persona','persona.id','=','solicitud.id_persona')
                                ->where('solicitud.tipo','=','asegurado')
                                ->whereRaw("CONCAT_WS(numero,' ',nombres,' ',apellidos,' ',razon_social) like ?",'%'.$prm.'%')
                                ->get();
                 $data = [];

                foreach ($asegurado as $id => $item) {
                    // dd($item);
                    if($item->razon_social != null){
                        $texto = $item->numero . ' ' . $item->razon_social;    
                    }else{
                        $texto = $item->numero . ' ' . $item->nombres . ' ' . $item->apellidos;    
                    }

                    if (strpos(strtolower($texto), $input['search']) !== false) {
                        $data[] = ['id' => $item->id.'_'.$item->numero, 'text' => ($texto)];
                    }
                }
                return \Response::json(array_slice($data, 0, 1000));

            }catch(Exception $e){
              return \Response::json( "error" );

            }

        }else
            return \Response::json(null);
    }

    public function validar_numero(Request $request){
        $input = $request->all();
        switch ($input['op']) {
            case 'validar_numero_siniestro':
                $data = Solicitud::where('numero','=',$input['id'])->first();
                return  $data != null ? true : false;
                break;
        }
    }
    
    public function index()
    {
        $data = Solicitud::all();
        return view('pages.siniestros.listado',[
            'data'=>$data
        ]);
    }
    public function paso_uno($id = null)
    {
        $tipo_doc = TipoDoc::pluck('nombre','id');
        $marcas = Marca::pluck('nombre','idmarca');

        if($id != null){
            $edit = Solicitud::where('id_solicitud',$id)->first();

            $modelos = Modelo::where('idmarca',$edit->vehiculo->marca)->pluck('nombre','idmodelo');
            $data_asegurado = [];
            $id_prm_asegurado = null;
            if($edit->tipo == 'tercero'){
                $res_asegurado = DB::table('solicitud')
                                    ->select('solicitud.id','solicitud.numero','persona.razon_social','persona.nombres','persona.apellidos')
                                    ->join('persona','persona.id','=','solicitud.id_persona')
                                    ->where('solicitud.id','=',$edit->id_asegurado)
                                    ->first(); 

                 $id_prm_asegurado = $res_asegurado->id.'_'.$res_asegurado->numero;
                if($res_asegurado->razon_social != null){
                    $data_asegurado[$res_asegurado->id.'_'.$res_asegurado->numero] = $res_asegurado->razon_social.'';
                }
                else{
                    $data_asegurado[$res_asegurado->id.'_'.$res_asegurado->numero] = $res_asegurado->nombres.' '.$res_asegurado->apellidos;
                }
                
            }
            return view('pages.siniestros.info_basica',['edit'=> $edit,'id'=>$id,'tipo_doc'=>$tipo_doc,'marcas'=>$marcas,'modelos'=>$modelos,'data_asegurado'=>$data_asegurado,'id_prm_asegurado' => $id_prm_asegurado]);
        }
        return view('pages.siniestros.info_basica',[
            'tipo_doc'=>$tipo_doc,
            'marcas'=>$marcas
        ]);
    }
    public function paso_uno_update(Request $request, $id){

        $input = $request->all();
        
        $data_pro = [];
        if(isset($input['nombres_d'])){
           foreach($input['nombres_d'] as $key => $item){
                if($item != null){
                    $data_pro[$key] = ['nombres'=>$item,'apellidos'=>$input['apellidos_d'][$key],'tipo_documento'=>$input['tipo_documento_d'][$key],'num_documento'=>$input['num_documento_d'][$key]];
                }
            }  
        }
       
        
         Validator::make($input, [
            'numero_siniestro' => ['required', 'string', 'max:100'],
            'tipo_perdida' => ['required'],
            'tipo_asegurado' => ['required'],
            'placa' => ['required'],
            'serie' => ['required'],
            'correo' => ['email'],
            'anio' => ['integer'],
            'marca' => ['integer'],
            'modelo' => ['integer'],
            'boleta_informativa'=>['mimes:pdf']
        ])->validate();

        $solicitud = Solicitud::where('id_solicitud',$id)->first();

        if($request->file('boleta_informativa') != null){
            $mime = $request->file('boleta_informativa')->getMimeType();
            $extension = strtolower($request->file('boleta_informativa')->getClientOriginalExtension());
            $name = $solicitud->id_vehiculo.'_'.$solicitud->id_persona.'_'.'boleta_informativa_'.date("Y-m-d_H_i_s").'.'.$extension;
            $path = base_path() . '/public/uploads/boletas_informativas/';
            $request->file('boleta_informativa')->move($path, $name);
        }

        
        $solicitud->numero = $input['numero_siniestro'];
        $solicitud->tipo = $input['tipo_asegurado'];
        $solicitud->perdida = $input['tipo_perdida'];

        if($request->file('boleta_informativa') != null){
            $solicitud->boleta_informativa = $name;
            $solicitud->date_boleta_informativa = date('Y-m-d h:i:s');
        }
        if($input['persona'] == 'natural')
            $solicitud->propietarios = json_encode($data_pro);
        else
            $solicitud->propietarios = null;

        $id_asegurado = null;
        
        if(isset($input['id_asegurado'])){
            $prm_solicitud = explode('_',$input['id_asegurado']);
            $id_asegurado = count($prm_solicitud) > 1 ? $prm_solicitud[0] : $input['id_asegurado'];
        }

        if($id_asegurado != null){
            $solicitud->id_asegurado = $id_asegurado;
        }

        if($solicitud->save()){
            $historico = Historico::where([
                ['id_solicitud','=',$solicitud->id],
                ['estado','=','registrado']
            ])->first();

            $historico->id_usuario_update = auth()->user()->id;
            $historico->save();
        }

        $Vehiculo = Vehiculo::find($solicitud->id_vehiculo);
        $Vehiculo->placa = $input['placa'];
        $Vehiculo->marca = $input['marca'];
        $Vehiculo->modelo = $input['modelo'];
        $Vehiculo->anio = $input['anio'];
        $Vehiculo->serie = $input['serie'];
        $Vehiculo->color = $input['color'];
        $Vehiculo->motor = $input['motor'];
        $Vehiculo->save();

        $persona = Persona::find($solicitud->id_persona);
        $persona->nombres = $input['nombres'];
        $persona->apellidos = $input['apellidos'];
        $persona->id_documento = $input['tipo_documento'];
        $persona->documento = $input['num_documento'];
        $persona->correo = $input['correo'];
        $persona->telefono = $input['telefono'];
        $persona->tipo = $input['persona'];
        if($input['persona'] == 'natural'){
            $persona->razon_social = null;
            $persona->nro_ruc = null;
        }
        else{
            $persona->razon_social = $input['razon_social'];
            $persona->nro_ruc = $input['nro_ruc'];
        }
        $persona->save();


        /* 29-06-2023 -> Registros de desistimiento y recuperación de auto*/
        //----Recuperacion-------------------------------------------
        if($input['cambio_recuperacion'] == 'true'){
            $id_recuperacion = Recuperacion::where('id_siniestro','=',$solicitud->id)->first();
            //dd($id_recuperacion);
            if($id_recuperacion != null){
                $id_recuperacion->estado = isset($input['vehiculo_recuperacion']);
                $id_recuperacion->motivo = isset($input['vehiculo_recuperacion']) ? $input['text_vehiculo_recuperacion'] : null;
                $id_recuperacion->fecha_updated = date('Y-m-d H:i:s');
                $id_recuperacion->user_id = auth()->user()->id;
                $id_recuperacion->save();
            }else{
                $data_save_recuperacion = new Recuperacion();
                $data_save_recuperacion->id_siniestro = $solicitud->id;
                $data_save_recuperacion->estado = isset($input['vehiculo_recuperacion']);
                $data_save_recuperacion->motivo = isset($input['vehiculo_recuperacion']) ? $input['text_vehiculo_recuperacion'] : null;
                $data_save_recuperacion->user_id = auth()->user()->id;
                $data_save_recuperacion->save();
            }
        }
        //----Desistimiento------------------------------------------
        if($input['cambio_desistimiento'] == 'true'){
            DB::table('solicitud_desistimiento')->insert([
                'id_siniestro' => $solicitud->id,
                'estado' => isset($input['desistimiento']),
                'motivo' => isset($input['desistimiento']) ? $input['text_desistimiento'] : null,
                'user_id' => auth()->user()->id
            ]);
            if(isset($input['desistimiento'])){
                $solicitud->estado = 'finiquito';
                $solicitud->save();

                Alert::toast('Registro actualizado correctamente.','success');
                return redirect()->route('siniestro.index');
            }else{
                $solicitud->estado = 'registrado';
                $solicitud->save();
            }
        }
        
        if(isset($input['desistimiento'])){
            Alert::toast('Registro actualizado correctamente.','success');
            return redirect()->route('siniestro.index');
        }
        //-End Cambio 29-06.2023------------------------------------------------------
        Alert::toast('Paso 1 actualizado correctamente.','success');
        return redirect()->route('siniestro.paso_2',$id);
    }

    public function paso_uno_store(Request $request){
        $input = $request->all();

        $data_pro = [];
        if(isset($input['nombres_d'])){
            foreach($input['nombres_d'] as $key => $item){
                if($item != null){
                    $data_pro[$key] = ['nombres'=>$item,'apellidos'=>$input['apellidos_d'][$key],'tipo_documento'=>$input['tipo_documento_d'][$key],'num_documento'=>$input['num_documento_d'][$key]];
                }
            }
        }
        
        if($input['tipo_asegurado'] == 'tercero'){
            $data_val = [
                'numero_siniestro' => ['required', 'string', 'max:100'],
                'tipo_perdida' => ['required'],
                'tipo_asegurado' => ['required'],
                'placa' => ['required'],
                'serie' => ['required'],
                'correo' => ['email'],
                'anio' => ['integer'],
                'marca' => ['integer'],
                'modelo' => ['integer'],
                'boleta_informativa'=>['mimes:pdf']
            ];
        }else{
              $data_val = [
                'numero_siniestro' => ['required', 'string', 'max:100','unique:solicitud,numero'],
                'tipo_perdida' => ['required'],
                'tipo_asegurado' => ['required'],
                'placa' => ['required'],
                'serie' => ['required'],
                'correo' => ['email'],
                'anio' => ['integer'],
                'marca' => ['integer'],
                'modelo' => ['integer'],
                'boleta_informativa'=>['mimes:pdf']
            ];
        }
        $id_asegurado = null;
        if(isset($input['id_asegurado'])){
            $prm_solicitud = explode('_',$input['id_asegurado']);
            $id_asegurado = count($prm_solicitud) > 1 ? $prm_solicitud[0] : $input['id_asegurado'];
        }
        
        Validator::make($input,$data_val)->validate();
        
        
        //--------------------------------------------
        $Vehiculo = new Vehiculo();
        $Vehiculo->placa = $input['placa'];
        $Vehiculo->marca = $input['marca'];
        $Vehiculo->modelo = $input['modelo'];
        $Vehiculo->anio = $input['anio'];
        $Vehiculo->serie = $input['serie'];
        $Vehiculo->color = $input['color'];
        $Vehiculo->motor = $input['motor'];
        $Vehiculo->save();
        //--------------------------------------
        $persona = new Persona();
        $persona->nombres = $input['nombres'];
        $persona->apellidos = $input['apellidos'];
        $persona->id_documento = $input['tipo_documento'];
        $persona->documento = $input['num_documento'];
        $persona->correo = $input['correo'];
        $persona->telefono = $input['telefono'];
        $persona->tipo = $input['persona'];
        if($input['persona'] == 'natural'){
            $persona->razon_social = null;
            $persona->nro_ruc = null;
        }else{
            $persona->razon_social = $input['razon_social'];
            $persona->nro_ruc = $input['nro_ruc'];
        }
        
        $persona->save();
        //-------------------------------------------
        if($request->file('boleta_informativa') != null){
            $mime = $request->file('boleta_informativa')->getMimeType();
            $extension = strtolower($request->file('boleta_informativa')->getClientOriginalExtension());
            $name = $Vehiculo->id.'_'.$persona->id.'_'.'boleta_informativa_'.date("Y-m-d_H_i_s").'.'.$extension;
            $path = base_path() . '/public/uploads/boletas_informativas/';
            $request->file('boleta_informativa')->move($path, $name);
        }
       
        //----------------------------------------------
        $id_solicitud = Str::random(36);
        $solicitud = new Solicitud();
        $solicitud->id_solicitud = $id_solicitud;
        $solicitud->id_vehiculo = $Vehiculo->id;
        $solicitud->id_persona = $persona->id;

        if($id_asegurado != null){
            $solicitud->id_asegurado = $id_asegurado;
        }

        $solicitud->numero = $input['numero_siniestro'];
        $solicitud->tipo = $input['tipo_asegurado'];
        $solicitud->perdida = $input['tipo_perdida'];
        if($request->file('boleta_informativa') != null){
            $solicitud->boleta_informativa = $name;
            $solicitud->date_boleta_informativa = date('Y-m-d h:i:s');    
        }
        $solicitud->estado  = 'registrado';
        if ($input['persona'] == 'natural'){
            $solicitud->propietarios = json_encode($data_pro);
            $solicitud->contacto = null;
        }
        else{
            $solicitud->propietarios = null;
            $solicitud->contacto = $input['contacto'];
        }
        $solicitud->usuario_id = auth()->user()->id;
        if($solicitud->save()){
            //--Historico de los movimientos------------------------
            $historico = new Historico();
            $historico->id_solicitud = $solicitud->id;
            $historico->estado = 'registrado';
            $historico->documento = true;
            $historico->nombre_documento = 'boleta_informativa';
            $historico->id_usuario_save = auth()->user()->id;
            $historico->save();
            //--Envio de correo------------------
            $correo = Correo::where('id_seccion','1')->first();
            if($correo != null){
                foreach(json_decode($correo->correos) as $item){
                    Mail::to($item)->send(new Siniestro($correo->texto,$correo->asunto.' ('.$solicitud->numero.','.$solicitud->vehiculo->placa.')','infosiniestros@esiscad.com'));
                }
            }
            //--02-07-2023 Asignar fotos a siniestro-------------------------------------
            $data_placa_fotos = Placa::find($input['asignar_fotos']);
            
            $solicitud->fotos = $data_placa_fotos->fotos;
            $solicitud->save();
            
            $data_placa_fotos->estado = 2;
            $data_placa_fotos->save();
        }
        Alert::toast('Paso 1 registrado correctamente.','success');
        return redirect()->route('siniestro.paso_2',$id_solicitud);
    }
    public function paso_dos($id){
        $archivo = Solicitud::where('id_solicitud',$id)->first();
        return view('pages.siniestros.subir_archivos',[
            'id'=>$id,
            'archivo'=>$archivo->carta,
            'numero'=>$archivo->numero
        ]);
    }
    public function paso_dos_store(Request $request,$id)
    {
        $input = $request->all();
        $solicitud = Solicitud::where('id_solicitud',$id)->first();

        if($input['frm_accion'] == 1){
            Validator::make($input, [
                'carta_perdida_total'=>['required','mimes:pdf']
            ])->validate();
        }else if($input['frm_accion'] == 2 && $solicitud->carta == null){
            Validator::make($input, [
                'carta_perdida_total'=>['required','mimes:pdf']
            ])->validate();
        }
        
        if($request->file('carta_perdida_total') != null){
            $mime = $request->file('carta_perdida_total')->getMimeType();
            $extension = strtolower($request->file('carta_perdida_total')->getClientOriginalExtension());
            $name = $id.'_'.'carta_perdida_total'.date("Y-m-d_H_i_s").'.'.$extension;
            $path = base_path() . '/public/uploads/carta/';
            $request->file('carta_perdida_total')->move($path, $name);
            $solicitud->carta = $name;
            $solicitud->date_carta = date('Y-m-d H:i:s');
            $solicitud->estado = 'con_carta';
            if($solicitud->save()){
                //--Historico-------------------
                $historico = Historico::where([
                    ['id_solicitud','=',$solicitud->id],
                    ['estado','=','con_carta']
                ])->first();
                if($historico != null){
                    $historico->id_usuario_update = auth()->user()->id;
                    $historico->save();
                }else{
                    $historico_store = new Historico();
                    $historico_store->id_solicitud = $solicitud->id;
                    $historico_store->estado = 'con_carta';
                    $historico_store->documento = true;
                    $historico_store->nombre_documento = 'carta';
                    $historico_store->id_usuario_save = auth()->user()->id;
                    $historico_store->save();
                }
            }
        }
       
        
        if($input['frm_accion'] == '1'){
            if($request->file('carta_perdida_total') != null)
                    Alert::toast('Documento cargado correctamente.','success');
            return redirect()->route('siniestro.paso_2',$id);
        }else{
            Alert::toast('Paso 2 registrado correctamente.','success');
            return redirect()->route('siniestro.paso_3',$id);
        }
    }
    public function paso_tres($id){
        $solicitud = Solicitud::where('id_solicitud',$id)->first();
        $expediente = Expediente::where('id_solicitud',$solicitud->id)->get();
        $selec_doc = Expediente::select('id_documento')->where('id_solicitud',$solicitud->id)->distinct()->get()->toArray();
        
        $documentos = Documentos::where('estado',1)->where(function($q)use($selec_doc){
            if(count($selec_doc) > 0){
                $q->whereNotIn('id',$selec_doc);
            }
        })->pluck('nombre','id');
        $documentos[0]='OTROS DOCUMENTOS';
        return view('pages.siniestros.documentos',[
            'documentos'=>$documentos,
            'expediente'=>$expediente,
            'id'=>$id,
            'numero'=>$solicitud->numero
        ]);   
    }
    public function paso_tres_store(Request $request,$id){
        $solicitud = Solicitud::where('id_solicitud',$id)->first();
        $input = $request->all();
        if($solicitud->estado == 'con_carta')
            $solicitud->estado = 'carga_documentos';
        $solicitud->save();
        if($input['frm_accion'] == '1'){
            Validator::make($input, [
                'tipo_documento'=>['required'],
                'documento'=>['required','mimes:pdf']
            ])->validate();
        }
        

        if($input['frm_accion'] == '1'){
            $mime = $request->file('documento')->getMimeType();
            $extension = strtolower($request->file('documento')->getClientOriginalExtension());
            $name = $id.'_'.'documento'.date("Y-m-d_H_i_s").'.'.$extension;
            $path = base_path() . '/public/uploads/documentos/';
            $request->file('documento')->move($path, $name);

            $expediente = Expediente::where('id_solicitud',$solicitud->id)->first();

            $new_fiel = new Expediente();
            $new_fiel->id_solicitud = $solicitud->id;
            $new_fiel->id_documento = $input['tipo_documento'];
            $new_fiel->ruta = $name;
            $new_fiel->tipo = 'pdf';
            $new_fiel->comentario = $input['comentario'];
            $new_fiel->save();

            $historico = new Historico();
            $historico->id_solicitud = $solicitud->id;
            $historico->estado = 'carga_documentos';
            $historico->documento = true;
            $historico->id_expediente = $new_fiel->id;
            $historico->id_usuario_save = auth()->user()->id;
            $historico->save();
            
            Alert::toast('Documento cargado correctamente.','success');
            return redirect()->route('siniestro.paso_3',$id);
        }
        if($input['frm_accion'] == '3'){
            $expediente = Expediente::find($input['id_reemplazo']);

            if($request->file('reemplazo_archivo') != null){
                $mime = $request->file('reemplazo_archivo')->getMimeType();
                $extension = strtolower($request->file('reemplazo_archivo')->getClientOriginalExtension());
                $name = $input['id_reemplazo'].'_'.'documento'.date("Y-m-d_H_i_s").'.'.$extension;
                $path = base_path() . '/public/uploads/documentos/';
                $request->file('reemplazo_archivo')->move($path, $name);

                $historico = Historico::where([
                    ['id_solicitud','=',$solicitud->id],
                    ['id_expediente','=',$expediente->id]
                ])->first();
                $historico->id_usuario_update = auth()->user()->id;
                $historico->save();

                $expediente->ruta = $name;
                $expediente->tipo = 'pdf';
            }
            $expediente->comentario = $input['modal_comentario'];
            $expediente->save();
          

            Alert::toast('Información actualizada correctamente.','success');
            return redirect()->route('siniestro.paso_3',$id);
        }
        if($solicitud->estado == 'carga_documentos')
            $solicitud->estado = 'por_tramitar';
        if($solicitud->save()){
             //--Historico-------------------
                $historico = Historico::where([
                    ['id_solicitud','=',$id],
                    ['estado','=','por_tramitar']
                ])->first();
                if($historico != null){
                    $historico->id_usuario_update = auth()->user()->id;
                    $historico->save();
                }else{
                    $historico_store = new Historico();
                    $historico_store->id_solicitud = $solicitud->id;
                    $historico_store->estado = 'por_tramitar';
                    $historico_store->documento = false;
                    $historico_store->id_usuario_save = auth()->user()->id;
                    $historico_store->save();
                }
        }
        Alert::toast('Paso 3 registrado correctamente.','success');
        return redirect()->route('siniestro.paso_4',$id);
    }
    public function paso_cuatro($id){
        $notarias = Notaria::pluck('nombre','id');
        $edit = Solicitud::where('id_solicitud',$id)->first();
        return view('pages.siniestros.notaria',[
            'notarias'=>$notarias,
            'id'=>$id,
            'id_notaria'=>$edit->id_notaria,
            'numero'=>$edit->numero
        ]);
    }
    public function paso_cuatro_store(Request $request,$id){        
        $input = $request->all();
        
        Validator::make($input, [
                'notaria'=>['required'],
            ])->validate();

        $solicitud = Solicitud::where('id_solicitud',$id)->first();
        $solicitud->id_notaria = $input['notaria'];
        $solicitud->estado = 'en_notaria';
        
        if($solicitud->save()){
             //--Historico-------------------
                $historico = Historico::where([
                    ['id_solicitud','=',$solicitud->id],
                    ['estado','=','en_notaria']
                ])->first();
                if($historico != null){
                    $historico->id_usuario_update = auth()->user()->id;
                    $historico->save();
                }else{
                    $historico_store = new Historico();
                    $historico_store->id_solicitud = $solicitud->id;
                    $historico_store->estado = 'en_notaria';
                    $historico_store->documento = false;
                    $historico_store->id_usuario_save = auth()->user()->id;
                    $historico_store->save();
                }

            $correo = Correo::where('id_seccion','2')->first();
            if($correo != null){
                foreach(json_decode($correo->correos) as $item){
                    Mail::to($item)->send(new Siniestro($correo->texto,$correo->asunto.' ('.$solicitud->numero.', '.$solicitud->vehiculo->placa.')','infosiniestros@esiscad.com'));
                }
            }
        }
        Alert::toast('Paso 4 registrado correctamente.','success');
        return redirect()->route('siniestro.paso_5',$id);   
    }
    public function paso_cinco($id){
        $edit = Solicitud::where('id_solicitud',$id)->first();
        return view('pages.siniestros.pago_derechos',[
            'id'=>$id,
            'edit'=>$edit
        ]);
    }
    public function paso_cinco_Store(Request $request,$id){
        
        $input = $request->all();
        
        Validator::make($input, [
                'pagos_notarias'=>['required'],
                'documento'=>['mimes:pdf']
        ])->validate();

        if($request->file('documento') != null){
            $mime = $request->file('documento')->getMimeType();
            $extension = strtolower($request->file('documento')->getClientOriginalExtension());
            $name = $id.'_'.'documento'.date("Y-m-d_H_i_s").'.'.$extension;
            $path = base_path() . '/public/uploads/adjuntos/';
            $request->file('documento')->move($path, $name);
        }
       

        $solicitud = Solicitud::where('id_solicitud',$id)->first();
        $solicitud->pagos = $input['pagos_notarias'];
        $solicitud->comentarios = $input['comentario'];
        $solicitud->kardex = $input['karder'];
        if($request->file('documento') != null){
            $solicitud->adjunto = $name; 
            $solicitud->date_adjunto = date('Y-m-d H:i:s');    
        }
        //if($solicitud->estado == 'en_notaria')
        $solicitud->estado = 'derechos_notariales';
        if($solicitud->save()){
            //--Historico-------------------
                $historico = Historico::where([
                    ['id_solicitud','=',$solicitud->id],
                    ['estado','=','derechos_notariales']
                ])->first();
                if($historico != null){
                    $historico->id_usuario_update = auth()->user()->id;
                    $historico->save();
                }else{
                    $historico_store = new Historico();
                    $historico_store->id_solicitud = $solicitud->id;
                    $historico_store->estado = 'derechos_notariales';
                    $historico_store->documento = true;
                    $historico_store->nombre_documento = 'adjunto';
                    $historico_store->id_usuario_save = auth()->user()->id;
                    $historico_store->save();
                }
        }

        Alert::toast('Registro almacenado correctamente.','success');
        return redirect()->route('home');
    }
    public function acta_inicial(Request $request){
        $input = $request->all();
        $data = Solicitud::where('id_solicitud',$input['id_solicitud'])->first();

        Validator::make($input, [
                'reemplazo_archivo'=>['required','mimes:pdf']
        ])->validate();

        if($request->file('reemplazo_archivo') != null){
            $mime = $request->file('reemplazo_archivo')->getMimeType();
            $extension = strtolower($request->file('reemplazo_archivo')->getClientOriginalExtension());
            $name = $input['id_solicitud'].'_'.'acta_inicial'.date("Y-m-d_H_i_s").'.'.$extension;
            $path = base_path() . '/public/uploads/acta_inicial/';
            $request->file('reemplazo_archivo')->move($path, $name);
            $data->acta_inicial = $name;
            $data->date_acta_inicial = date('Y-m-d H:i:s');
            $data->estado = 'con_acta_inicial';
            
            if($data->save()){
                //--Historico-------------------
                $historico = Historico::where([
                    ['id_solicitud','=',$data->id],
                    ['estado','=','con_acta_inicial']
                ])->first();
                if($historico != null){
                    $historico->id_usuario_update = auth()->user()->id;
                    $historico->save();
                }else{
                    $historico_store = new Historico();
                    $historico_store->id_solicitud = $data->id;
                    $historico_store->estado = 'con_acta_inicial';
                    $historico_store->documento = true;
                    $historico_store->nombre_documento = 'acta_inicial';
                    $historico_store->id_usuario_save = auth()->user()->id;
                    $historico_store->save();
                }
               $correo = Correo::where('id_seccion','3')->first();
                if($correo != null){
                    foreach(json_decode($correo->correos) as $item){
                        Mail::to($item)->send(new Siniestro($correo->texto,$correo->asunto.' ('.$data->numero.' ,'.$data->vehiculo->placa.')','infosiniestros@esiscad.com'));
                    }
                } 
            }
        }

        Alert::toast('Acta inical cargada correctamente.','success');
        return redirect()->route('siniestro.index');
    }
    public function acta_final(Request $request){
        $input = $request->all();
        
        $data = Solicitud::where('id_solicitud',$input['id_solicitud_final'])->first();

        Validator::make($input, [
                'reemplazo_archivo'=>['required','mimes:pdf']
        ])->validate();

        if($request->file('reemplazo_archivo') != null){
            $mime = $request->file('reemplazo_archivo')->getMimeType();
            $extension = strtolower($request->file('reemplazo_archivo')->getClientOriginalExtension());
            $name = $input['id_solicitud_final'].'_'.'acta_final'.date("Y-m-d_H_i_s").'.'.$extension;
            $path = base_path() . '/public/uploads/acta_final/';
            $request->file('reemplazo_archivo')->move($path, $name);
            $data->acta_final = $name;
            $data->estado = 'con_acta_final';
            $data->date_acta_final = date('Y-m-d H:i:s');
            
            if($data->save()){
                //--Historico-------------------
                $historico = Historico::where([
                    ['id_solicitud','=',$data->id],
                    ['estado','=','con_acta_final']
                ])->first();
                if($historico != null){
                    $historico->id_usuario_update = auth()->user()->id;
                    $historico->save();
                }else{
                    $historico_store = new Historico();
                    $historico_store->id_solicitud = $data->id;
                    $historico_store->estado = 'con_acta_final';
                    $historico_store->documento = true;
                    $historico_store->nombre_documento = 'acta_final';
                    $historico_store->id_usuario_save = auth()->user()->id;
                    $historico_store->save();
                }
                $correo = Correo::where('id_seccion','4')->first();
                if($correo != null){
                    foreach(json_decode($correo->correos) as $item){
                        Mail::to($item)->send(new Siniestro($correo->texto,$correo->asunto.' ('.$data->numero.' ,'.$data->vehiculo->placa.')','infosiniestros@esiscad.com'));
                    }
                } 
            }
        }
        
        Alert::toast('Acta final cargada correctamente.','success');
        return redirect()->route('siniestro.index');
    }
    public function show($id)
    {
        $data = Solicitud::where('id_solicitud',$id)->first();
        return view('pages.siniestros.show',[
            'data'=>$data
        ]);
    }
    public function edit($id)
    {
        $data = Solicitud::where('id_solicitud',$id)->first();
        $ubicaciones = $data->data_deposito != null ? DB::table('inv_locator')->where('deposito_id',$data->data_deposito->id)->pluck(DB::raw("concat(segment1,' ',segment2,' ') as nombre"),'id') : [];
        $talleres = Taller::pluck('nombre','id');
        
        $depositos = Deposito::pluck('nombre','id');
        return view('pages.siniestros.edit',[
            'data'=>$data,
            'depositos'=>$depositos,
            'ubicaciones'=>$ubicaciones,
            'talleres'=>$talleres
        ]);
    }
    public function ajax($id){
        $data = Ubicacion::where('deposito_id',$id)->get();
        return json_encode($data);
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
        Validator::make($input, [
                'deposito'=>['required']
        ])->validate();

        $edit = Solicitud::where('id_solicitud',$id)->first();
        $edit->llave = isset($input['llaves']) ? 'si' : 'no';
        $edit->tarjeta = isset($input['tarjeta']) ? 'si' : 'no';
        $edit->id_deposito = $input['deposito'];
        //$edit->id_ubicacion = $input['ubicacion'];
        $edit->observaciones = $input['observaciones'];
        $edit->id_taller = $input['taller'];
        
        if($edit->save()){
             //--Historico-------------------
                $historico = Historico::where([
                    ['id_solicitud','=',$edit->id],
                    ['estado','=','asignacion_deposito']
                ])->first();
                if($historico != null){
                    $historico->id_usuario_update = auth()->user()->id;
                    $historico->save();
                }else{
                    $historico_store = new Historico();
                    $historico_store->id_solicitud = $edit->id;
                    $historico_store->estado = 'asignacion_deposito';
                    $historico_store->documento = false;
                    $historico_store->id_usuario_save = auth()->user()->id;
                    $historico_store->save();
                }
        }
        Alert::toast('Registro actualizado correctamente.','success');
        return redirect()->route('siniestro.index');
    }
    public function salida($id){
        $data = Solicitud::where('id_solicitud',$id)->first();
        return view('pages.siniestros.salida',[
            'data'=>$data
        ]);
    }
    public function salida_store(Request $request,$id){
        $input = $request->all();
        Validator::make($input, [
                'entrego_a'=>['required'],
                'adjunto'=>['required','mimes:pdf']
        ])->validate();

         if($request->file('adjunto') != null){
            $mime = $request->file('adjunto')->getMimeType();
            $extension = strtolower($request->file('adjunto')->getClientOriginalExtension());
            $name = $id.'_'.'salida_adjunto'.date("Y-m-d_H_i_s").'.'.$extension;
            $path = base_path() . '/public/uploads/adjuntos/';
            $request->file('adjunto')->move($path, $name);
        }else
            $name = null;
        
        $data = Solicitud::where('id_solicitud',$id)->first();
        $data->adjunto_salida = $name;
        $data->entrego_a = $input['entrego_a'];
        $data->estado = 'con_salida';
        
        if($data->save()){
             //--Historico-------------------
                $historico = Historico::where([
                    ['id_solicitud','=',$data->id],
                    ['estado','=','con_salida']
                ])->first();
                if($historico != null){
                    $historico->id_usuario_update = auth()->user()->id;
                    $historico->save();
                }else{
                    $historico_store = new Historico();
                    $historico_store->id_solicitud = $data->id;
                    $historico_store->estado = 'con_salida';
                    $historico_store->documento = true;
                    $historico_store->nombre_documento = 'adjunto_salida';
                    $historico_store->id_usuario_save = auth()->user()->id;
                    $historico_store->save();
                }
        }
        Alert::toast('Salida registrada correctamente.','success');
        return redirect()->route('siniestro.index');
        
    }

    public function fotos($id){
        $data = Solicitud::where('id_solicitud',$id)->first();  

        if($data == null){
            $data = Placa::where('id','=',$id)->whereNull('id_siniestro')->first();
            if($data == null){
                $data = Placa::where([['placa','=',$id],['estado','=','1']])->whereNull('id_siniestro')->first();
            }
        }
        
        return view('pages.siniestros.fotos',[
            'id'=>$id,
            'data'=>$data
        ]);
    }
    public function fotos_trgister(Request $request){
        $input = $request->all();
        $data_save = new Placa();
        $data_save->placa = $input['placa'];
        $data_save->user_id = auth()->user()->id;
        $data_save->estado = 1;
        $data_save->save();

        return json_encode([
                                'success' => true,
                                'data' => $data_save->id
                            ]);
    }
    public function fotos_store(Request $request,$id){
        //Alert::toast('Cargando fotos','info')->timerProgressBar(true);
        //sleep(10);
        $input = $request->all();
        $aux = 0;
        
        $fotos = [];
        if(isset($input['name_foto'])){
            for($i = 0 ; $i < count($input['name_foto']) ; $i++){
                if($input['name_foto'][$i] != '0'){
                    $fotos[$aux] = $input['name_foto'][$i];
                    $aux++;
                }
            }
        }
       
       if(isset($input['file_input'])){
        for($i = 0 ; $i < count($input['file_input']); $i++){
            if(isset($request->file('file_input')[$aux])){
                if($request->file('file_input')[$aux] != null){
                    $mime = $request->file('file_input')[$aux]->getMimeType();
                    $extension = strtolower($request->file('file_input')[$aux]->getClientOriginalExtension());
                    $name = $id.'_'.$aux.'_foto_'.date("Y-m-d_H_i_s").'.'.$extension;
                    $path = base_path() . '/public/uploads/fotos/';
                    $request->file('file_input')[$aux]->move($path, $name);
                    $fotos[$aux] = $name;
                    $aux++;
                }
            }
        }
       }
        
        if(count($fotos)>0){
            
            $data = Solicitud::where('id_solicitud',$id)->first();

            if($data != null){
                $data->fotos = json_encode($fotos);
                $data->save();    
            }else{
                $data_placa = Placa::where('id','=',$id)->whereNull('id_siniestro')->first();
                if($data_placa == null){
                    $data_placa = Placa::where('placa','=',$id)->whereNull('id_siniestro')->whereNull('fotos')->first();
                    $data_placa->fotos = json_encode($fotos);
                    $data_placa->estado = 1;
                    $data_placa->save();     
                }else{
                    $data_placa->fotos = json_encode($fotos);
                    $data_placa->estado = 1;
                    $data_placa->save();     
                }
            }

            Alert::toast('Fotos cargadas correctamente.','success');
            return redirect()->route('siniestro.index');
        }else{
            Alert::toast('Error al subir fotos.','warning');
            return redirect()->route('siniestro.fotos',$id);
        }   
    }
    public function qr($id){
        $data = Solicitud::where('id_solicitud',$id)->first();
        if($data->id_deposito == null){
            Alert::toast('Favor de asignar ubicación.','info');
            return redirect()->route('siniestro.index');       
        }
        $data->qr = $id;
        $data->save();
        Alert::toast('QR Generado correctamente.','success');
        return redirect()->route('siniestro.qr_ver',$id);        
    }
    public function qr_ver($id){
        return view('pages.siniestros.ver_qr',[
            'data'=>$id
        ]);
    }
    public function informacion($id){
        $data = Solicitud::where('id_solicitud',$id)->first();
        return view('pages.siniestro',[
            'data'=>$data
        ]);
    }
    public function imprimir_qr($id){
        return view('pages.qr_imprimir',[
            'data'=>$id
        ]);
    }
    public function bitacora($id){
        $data = Solicitud::where('id_solicitud',$id)->first();
        return PDF::loadView('pages.siniestros.bitacora',[
            'data'=>$data
        ])->stream('bitacora.pdf');//->download('bitacora.pdf');//->stream('bitacora.pdf');
    }
     public function reporte($id){
        $data = Solicitud::where('id_solicitud',$id)->first();
        return PDF::loadView('pages.siniestros.reporte_deposito',[
            'data'=>$data,
            'pdf'=> new PDF()
        ])->stream('reporte_deposito.pdf');//->download('bitacora.pdf');//->stream('bitacora.pdf');
    }

    public function paso_seis_index($id){
        $data = Solicitud::where('id_solicitud',$id)->first();
        
       if($data->id_deposito == null && ($data->acta_final == null || $data->acta_inicial == null) ){
            Alert::toast('Falta asignar deposito y/o Acta Final, Acta Inicla.','warning');
            return redirect()->route('siniestro.index');
        }

        return view('pages.siniestros.finiquito',[
            'data' => $data
        ]);
    }

    public function paso_seis_store(Request $request, $id){
        $input = $request->all();
        
        Validator::make($input, [
                'adjunto'=>['required','mimes:pdf']
        ])->validate();

         if($request->file('adjunto') != null){
            $mime = $request->file('adjunto')->getMimeType();
            $extension = strtolower($request->file('adjunto')->getClientOriginalExtension());
            $name = $id.'_'.'adjunto_finiquito'.date("Y-m-d_H_i_s").'.'.$extension;
            $path = base_path() . '/public/uploads/finiquito/';
            $request->file('adjunto')->move($path, $name);
        }else
            $name = null;

        $data = Solicitud::where('id_solicitud',$id)->first();
        $data->finiquito = $name;
        $data->date_finiquito = date('Y-m-d H:i:s');
        $data->estado = 'finiquito';
        
        if($data->save()){
             //--Historico-------------------
                $historico = Historico::where([
                    ['id_solicitud','=',$data->id],
                    ['estado','=','finiquito']
                ])->first();
                if($historico != null){
                    $historico->id_usuario_update = auth()->user()->id;
                    $historico->save();
                }else{
                    $historico_store = new Historico();
                    $historico_store->id_solicitud = $data->id;
                    $historico_store->estado = 'finiquito';
                    $historico_store->documento = true;
                    $historico_store->nombre_documento = 'adjunto_finiquito';
                    $historico_store->id_usuario_save = auth()->user()->id;
                    $historico_store->save();
                }
        }
        Alert::toast('Adjunto finiquito registrado correctamente.','success');
        return redirect()->route('siniestro.index');
    }
    public function validar_placa(Request $request){
        $input = $request->all();
        $id_placa = 0;
        
        $data_val = Placa::where([['placa','=',$input['placa']],['estado','=','1']])->first();

        if($data_val != null)
            $id_placa = $data_val->id;

        return json_encode($id_placa);
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
