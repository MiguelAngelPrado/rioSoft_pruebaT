<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Solicitud;
use App\Models\Modelo;
use App\Models\Placa;
use ZipArchive;
use DB;
use DateTime;

class NotariaController extends Controller
{
    public function getPasos(){
        return [
            1 => 'Paso 1 Información Basica de Vehiculo (Boleta informativa).',
            2 => 'Paso 2 Información Adjuta (Carta perdida total)',
            3 => 'Paso 3 Documentación',
            4 => 'Paso 4 Notaria',
            5 => 'Paso 5 Pago derechos notariales (Documento)'
        ];
    }
    public function index(Request $request)
    {
        $input = $request->all();
        //dd($input);
        $numero = isset($input['numero']) ? $input['numero'] : null;
        $placa = isset($input['placa']) ? $input['placa'] : null;
        $propietario = isset($input['propietario']) ? $input['propietario'] : null;
        $estado = isset($input['estado']) ? $input['estado'] : null;

        $uno = isset($input['uno_dia']) ? true : false;
        $dos = isset($input['dos_dia']) ? true : false;
        $tres = isset($input['tres_dia']) ? true : false;
       
        $data = Solicitud::select(DB::raw("solicitud.*,(select TIMESTAMPDIFF(DAY,updated_at,NOW()) from historico where historico.id_solicitud =solicitud.id AND  historico.estado=solicitud.estado limit 1) as fecha_historico,(select updated_at from historico where historico.id_solicitud =solicitud.id AND  historico.estado=solicitud.estado limit 1) as data_fecha"))
        ->where(function($q) use ($numero, $placa, $propietario, $uno, $dos, $tres){
            if($numero != null){
                $q->where('numero','like','%'.$numero.'%');
            }
            if(auth()->user()->getrol_user->getrol->name == 'notaria' && auth()->user()->getpersona != null){
                $q->where('id_notaria','=',auth()->user()->getpersona->id_notaria)
                ->whereNotNull('pagos');
            }
            if($uno){
                $q->whereRaw("(select TIMESTAMPDIFF(DAY,updated_at,NOW()) from historico where historico.id_solicitud =solicitud.id AND  historico.estado=solicitud.estad limit 1) < 2");
            }
            if($dos){
                $q->whereRaw("(select TIMESTAMPDIFF(DAY,updated_at,NOW()) from historico where historico.id_solicitud =solicitud.id AND  historico.estado=solicitud.estado limit 1) = 2 ");
            }
            if($tres){
                $q->whereRaw("(select TIMESTAMPDIFF(DAY,updated_at,NOW()) from historico where historico.id_solicitud =solicitud.id AND  historico.estado=solicitud.estado limit 1) > 2 ");
            }
            /*if(auth()->user()->getrol_user->getrol->name == 'gruero'){
                $q->whereNull('id_deposito');
            }*/
        })
        ->where(function($q) use ($estado){
            if($estado != null){
                if($estado == 'carga_documentos'){
                    $q->whereIn('estado',['carga_documentos','por_tramitar']);    
                }else{
                    $q->where('estado','=',$estado);    
                }
            }
        })
        ->whereIn('id_vehiculo',function($q) use ($placa){
            if($placa != null){
                $q->from('vehiculo')
                ->select('id')
                ->where('placa','like','%'.$placa.'%');
            }else{
                $q->from('vehiculo')
                ->select('id');
            }
        })
        ->whereIn('id_persona',function($q) use ($propietario){
            if($propietario != null){
                $q->from('persona')
                ->select('id')
                ->whereRaw("CONCAT_WS(' ',nombres,apellidos) like ?","%".$propietario.'%');
                
            }else{
                $q->from('persona')
                ->select('id');
            }
        })
        ->orWhere(function($q) use ($propietario){
            if($propietario != null){
                $q->from('solicitud')
                ->orWhereRaw("CONCAT_WS(' ',contacto) like ?","%".$propietario.'%');
            }
        });
        //dd($data->get());
        //$res = $data->paginate(2);
        //dd($res);
        //--Obtener los dias---------
        /*foreach($res as $key => $item){
            $historico =DB::table('historico')
            ->where([
                ['id_solicitud','=',$item->id],
                ['estado','=',$item->estado]
            ])
            ->first();

            $fecha1= new DateTime($historico->updated_at);
            $fecha2= new DateTime(Date('Y-m-d H:i:s'));
            $diff = $fecha1->diff($fecha2);
            $res[$key]['fecha_historico'] = $historico->updated_at;
            $res[$key]['dias_transcurridos'] = $diff->days;
                
        }*/
        //$filstro = $res->where('dias_transcurridos','<','7');
        //dd()
        
        $model = new Solicitud();
        
        $registrar_placa = 0;
        $placa_registrada = 0;
        if($data->count() == 0 && auth()->user()->getrol_user->getrol->name == 'gruero'){
            $data_m_placa = Placa::where('placa','=',$placa)->whereNull('id_siniestro')->first();
            if($data_m_placa != null){
                $registrar_placa = 2;
                $placa_registrada = $data_m_placa->id;
            }else{
                $registrar_placa = 1;    
            }
        }else if($data->count() != 0 && auth()->user()->getrol_user->getrol->name == 'gruero'){
            $registrar_placa = 3;    
        }

        return view('pages.notaria.lista',[
            'data'=>$data->paginate(10),
            'numero'=>$numero,
            'placa'=>$placa,
            'propietario'=>$propietario,
            'pasos'=>$this->getPasos(),
            'uno' => $uno,
            'dos' => $dos, 
            'tres' => $tres,
            'estado' => $estado,
            'model' => $model,
            'registrar_placa' => $registrar_placa,
            'placa_registrada' => $placa_registrada
        ]);
    }
    function editar_paso($id){
        $data = Solicitud::where('id_solicitud',$id)->first();
        $pasos =  
        [
            0 => ['texto'=>'Datos Generales','estatus'=>$data->numero != null ? true : false,'ruta'=>route('siniestro.paso_1',$id)],
            1 => ['texto'=>'Carta Perdida Total','estatus'=>$data->carta != null ? true : false,'ruta'=>route('siniestro.paso_2',$id)],
            2 => ['texto'=>'Otros Documentos','estatus'=> count($data->expediente) > 0 ? true : false,'ruta'=>route('siniestro.paso_3',$id)],
            3 => ['texto'=>'Asignar Notaria','estatus'=>$data->id_notaria != null ? true : false,'ruta'=>route('siniestro.paso_4',$id)],
            4 => ['texto'=>'Derechos Notariales','estatus'=>$data->pagos != null ? true : false,'ruta'=>route('siniestro.paso_5',$id)]
        ];
        if($data->perdida == 'danio'){
           array_push($pasos,['texto'=>'Adjuntar archivo Finiquito','estatus'=>$data->finiquito != null ? true : false,'ruta'=>route('siniestro.paso_seis_index',$id)]); 
        }
        return json_encode($pasos);
    }
    public function ajax_modelo($id){
        $data = Modelo::where('idmarca',$id)->get();
        return json_encode($data);
    }
    public function documentos($id){
        $carpeta = 'siniestro_'.date('d_m_Y');

        $data = Solicitud::where('id_solicitud',$id)->first();

        $zip = new ZipArchive();
        $zip->open($carpeta.".zip",ZipArchive::CREATE);

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
        
        //header("Content-type: application/octet-stream");
        //header("Content-disposition: attachment; filename=".$carpeta.".zip");
        
        readfile(public_path().'\uploads\comprimidos\\'.$carpeta.'.zip');
        //dd($zip);
        
        //$boleta_info = public_path().'/uploads/boletas_informativas/'.$data->boleta_informativa;
        //return Response::download($boleta_info, 'boleta_informativa_'.date('Y_m_d_His').'.pdf', $headers);
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
