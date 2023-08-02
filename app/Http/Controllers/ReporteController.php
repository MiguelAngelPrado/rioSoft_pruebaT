<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Documentos;
use App\Models\Solicitud;
use Maatwebsite\Excel\Excel;
use App\Models\Historico;
use App\Models\Expediente;
use App\Exports\ReporteGeneral;
use DB;

class ReporteController extends Controller
{
    public function getHeader($todos = true,$personalizado = false){
        if($todos){
            $header = [
                'numero_siniestro' => 'Siniestro',
                'placa' => 'Placa',
                'cliente' => 'cliente'
            ];

            if($personalizado){
                $header['tipo_asegurado'] = 'Tipo Asegurado';
                $header['tipo_perdida'] = 'Tipo Perdida';
                $header['estado_actual'] = 'Estado Actual';
                $header['f_estado_actual'] = 'Fecha Estado Actual';
                $header['desistimiento'] = 'Desistimiento';
                $header['vehiculo_recuperado'] = 'Vehiculo Recuperado';
            }

            $header['registrado'] = 'Datos Generales';
            $header['con_carta'] = 'Carta de Perdida Total';
        }else{
            $header = [
                'registrado' => 'Datos Generales',
                'con_carta' => 'Carta de Perdida Total',
            ];
        }
        
        $carga_documentos = [];
        $carga_documentos[0] = 'Otros';
        $data_doc = Documentos::where('estado',1)->get();

        foreach($data_doc as $item){
            $carga_documentos[$item->id] = $item->nombre;
        }
        $header['carga_documentos'] = $carga_documentos;
        $header['en_notaria'] = 'Selección de Notaria';
        $header['derechos_notariales'] = 'Pago derechos notariales';
        $header['con_acta_inicial'] = 'Acta Inicial';
        $header['con_acta_final'] = 'Acta Final';
        $header['asignacion_deposito'] = 'Vehiculo en Deposito';
        $header['finiquito'] = 'Terminado';
        return $header;
    }
    public function informacion_general(Request $request,Excel $excel)
    {
        $input = $request->all();
        
        $inicio = isset($input['inicio']) ? $input['inicio'] : null;
        $fin = isset($input['fin']) ? $input['fin'] : null;
        $danio = isset($input['danio']) ? true : false;
        $robo = isset($input['robo']) ? true : false;

        $tipo = isset($input['tipo']) ? $input['tipo'] : null;

        
        $header = $this->getHeader();

        $data = Solicitud::where(function($q) use ( $inicio, $fin, $danio, $robo){
            if($inicio != null){
                $q->where('created_at','>=',$inicio);
            }
            if($fin != null){
                $q->where('updated_at','<=',$fin);
            }
            if($danio){
                $q->where('perdida','=','danio');
            }
            if($robo){
                $q->where('perdida','=','robo');
            }
        })->get();

        if($tipo == 2){
            return $excel->download(new ReporteGeneral($this->getHeader(true,true),$data), 'Reporte_informacion_general'.time().'.xlsx');    
        }
        
        if($tipo == 3){
            return $excel->download(new ReporteGeneral($this->getHeader(true,true),$data), 'Reporte_informacion_general'.time().'.csv');    
        }

        $input = $request->all();
        return view('pages.reportes.info_general',[
            'headers' => $header,
            'data' => $data,
            'inicio' => $inicio,
            'fin' => $fin,
            'danio' => $danio,
            'robo' => $robo,
        ]);
    }
    public function informacion_general_grafica(Request $request){

        $input = $request->all();
        
        $inicio = isset($input['inicio']) ? $input['inicio'] : null;
        $fin = isset($input['fin']) ? $input['fin'] : null;
        $danio = isset($input['danio']) ? true : false;
        $robo = isset($input['robo']) ? true : false;


        $header = $this->getHeader(false);

         return view('pages.reportes.general_grafica',[
            'headers' => $header,
            'inicio' => $inicio,
            'fin' => $fin,
            'danio' => $danio,
            'robo' => $robo,
        ]);
    }
    public function informacion_general_grafica_java(Request $request){
        
        $input = $request->all();

        $danio = isset($input['danio']) ? $input['danio'] : null;
        $robo = isset($input['robo']) ? $input['robo'] : null;
        $inicio = isset($input['inicio']) ? $input['inicio'] : null;
        $fin = isset($input['fin']) ? $input['fin'] : null;

         $header = $this->getHeader(false);
         $documentos = Documentos::where('estado','=',1)->get();
         $data = [];
         $total_solicitud = Solicitud::select('id')->where(function($q) use ($robo, $danio){
            if($robo != null){
                if($robo == 'true'){
                    $q->where('perdida','=','perdida');
                }
            }
            if($danio != null){
                if($danio == 'true'){
                    $q->where('perdida','=','perdida');
                }
            }
         })->get()->toArray();
         
         foreach($header as $key => $item){
            //$row = Historico::where('estado','=',$key)->whereIn('id_solicitud',$total_solicitud)->distinct('id_solicitud')->count();
            $row = Solicitud::where('estado','=',$key)->whereIn('id',$total_solicitud)->distinct('id')->count();
            if($key == 'carga_documentos'){
                $data_expediente = [];
                $data_solicitud = Solicitud::whereIn('estado',['carga_documentos','por_tramitar'])->whereIn('id',$total_solicitud)->distinct('id')->count();
                /*foreach($documentos as $key2 => $doc){
                    $expediente = Expediente::select('id_solicitud')->distinct()->whereIn('expediente.id_solicitud',$total_solicitud)->where('id_documento','=',$doc->id)->count();
                    $data_expediente[$key2] = ['texto'=>$doc->nombre,'cantidad'=>$expediente,'id_documento'=>$doc->id];    
                }
                $data[$key] = $data_expediente;*/
                $data['carga_documentos'] = ['texto'=>'Carga de Documentos','cantidad'=>$data_solicitud];
            }else{
                $data[$key] = ['texto'=>$item,'cantidad'=>$row];
            }
            
         }
         if(isset($input['op'])){
            return json_encode(['data'=>$data]);
         }else
            return json_encode(['data'=>$data,'total'=>count($total_solicitud)]);
    }
    public function informacion_general_grafica_detalle($texto,$doc, $id_doc = null){
       $data = DB::table('solicitud')
                    ->select('solicitud.numero','vehiculo.placa','persona.nombres','persona.apellidos','persona.razon_social','solicitud.updated_at')
                    ->join('persona','persona.id','=','solicitud.id_persona')
                    ->leftjoin('vehiculo','vehiculo.id','=','solicitud.id_vehiculo')
                    ->join('expediente',function($q) use ($id_doc){
                        if($id_doc != 0){
                            $q->on('solicitud.id','=','expediente.id_solicitud')
                            ->where('expediente.id_documento','=',$id_doc);
                        }
                    })
                    ->where(function($q) use($id_doc,$doc){
                        if($doc != 'carga_documentos'){
                            $q->where('solicitud.estado','=',$doc);
                        }else{
                            $q->whereIn('solicitud.estado',['carga_documentos','por_tramitar']);
                        }
                    })
                    ->distinct('solicitud.id')
                    ->get();
        return view('pages.reportes.info_general_detalle',[
            'texto' => $texto,
            'data' => $data
        ]);
    }
    public function dashboard_detalle($texto,$doc, $id_doc = null){
       $data = DB::table('historico')
                    ->select('solicitud.numero','vehiculo.placa','persona.nombres','persona.apellidos','persona.razon_social','historico.updated_at',DB::raw('DATEDIFF(now(),historico.updated_at) as dias'))
                    ->join('solicitud','solicitud.id','=','historico.id_solicitud')
                    ->join('persona','persona.id','=','solicitud.id_persona')
                    ->leftjoin('vehiculo','vehiculo.id','=','solicitud.id_vehiculo')
                    ->join('expediente',function($q) use ($id_doc){
                        if($id_doc != 0){
                            $q->on('historico.id_expediente','=','expediente.id')
                            ->where('expediente.id_documento','=',$id_doc);
                        }
                    })
                    ->where('historico.estado','=',$doc)
                    ->distinct('solicitud.id')
                    ->get();

        return view('pages.reportes.detalle',[
            'texto' => $texto,
            'data' => $data
        ]);
      
    }
    public function dashboard()
    {
        $estados = [];
        $header = $this->getHeader(false);
        foreach($header as $key => $item){
            if($key != 'carga_documentos')
                $estados[$key] = $item;
            else{
                foreach($item as $key2 => $row){
                    $estados[$key.'_'.$key2] = $row;
                }
            }
        }

        $documentos = Documentos::where('estado','=',1)->get();
        $data = [];
        $total_solicitud = Solicitud::select('id')->get()->toArray();/*->where(function($q) use ($robo, $danio){
            if($robo != null){
                if($robo == 'true'){
                    $q->where('perdida','=','perdida');
                }
            }
            if($danio != null){
                if($danio == 'true'){
                    $q->where('perdida','=','perdida');
                }
            }
         })->get()->toArray();*/
         
         foreach($header as $key => $item){
            if($key == 'carga_documentos'){
                $data_expediente = [];
                foreach($documentos as $key2 => $doc){
                    $expediente = Expediente::select('id_solicitud')->distinct()->whereIn('id_solicitud',$total_solicitud)->where('id_documento','=',$doc->id)->count();
                    $exp_danio = Expediente::select('expediente.id_solicitud')->distinct()->join('solicitud','solicitud.id','=','expediente.id_solicitud')->where('solicitud.perdida','=','danio')->whereIn('expediente.id_solicitud',$total_solicitud)->where('id_documento','=',$doc->id)->count();
                    $exp_robo = Expediente::select('expediente.id_solicitud')->distinct()->join('solicitud','solicitud.id','=','expediente.id_solicitud')->where('solicitud.perdida','=','robo')->whereIn('expediente.id_solicitud',$total_solicitud)->where('id_documento','=',$doc->id)->count();
                    $data_expediente[$key2] = ['texto'=>$doc->nombre,'cantidad'=>$expediente,'id_documento'=>$doc->id,'robo'=>$exp_robo,'danio'=>$exp_danio];   
                }
                $data[$key] = $data_expediente;
            }else{
                $row = Historico::where('estado','=',$key)->whereIn('id_solicitud',$total_solicitud)->distinct('id_solicitud')->count();
                $danio = Historico::where('historico.estado','=',$key)->join('solicitud','solicitud.id','=','historico.id_solicitud')->where('solicitud.perdida','=','danio')->whereIn('historico.id_solicitud',$total_solicitud)->distinct('historico.id_solicitud')->count();
                $robo = Historico::where('historico.estado','=',$key)->join('solicitud','solicitud.id','=','historico.id_solicitud')->where('solicitud.perdida','=','robo')->whereIn('historico.id_solicitud',$total_solicitud)->distinct('historico.id_solicitud')->count();
                $data[$key] = ['texto'=>$item,'cantidad'=>$row,'robo'=>$robo,'danio'=>$danio];
            }
            
         }
        
        return view('pages.dashboard_graficas',[
            'data' => $data,
            'total' => count($total_solicitud),
            'estados' => $estados
        ]);
    }
    public function dashboard_ajax()
    {
        $estados = [];
        $header = $this->getHeader(false);
        foreach($header as $key => $item){
            if($key != 'carga_documentos')
                $estados[$key] = $item;
            else{
                foreach($item as $key2 => $row){
                    $estados[$key.'_'.$key2] = $row;
                }
            }
        }

        $documentos = Documentos::all();
        $data = [];
        $total_solicitud = Solicitud::select('id')->get()->toArray();/*->where(function($q) use ($robo, $danio){
            if($robo != null){
                if($robo == 'true'){
                    $q->where('perdida','=','perdida');
                }
            }
            if($danio != null){
                if($danio == 'true'){
                    $q->where('perdida','=','perdida');
                }
            }
         })->get()->toArray();*/
         
         foreach($header as $key => $item){
            if($key == 'carga_documentos'){
                $data_expediente = [];
                foreach($documentos as $key2 => $doc){
                    $expediente = Expediente::select('id_solicitud')->distinct()->whereIn('id_solicitud',$total_solicitud)->where('id_documento','=',$doc->id)->count();
                    $exp_danio = Expediente::select('expediente.id_solicitud')->distinct()->join('solicitud','solicitud.id','=','expediente.id_solicitud')->where('solicitud.perdida','=','danio')->whereIn('expediente.id_solicitud',$total_solicitud)->where('id_documento','=',$doc->id)->count();
                    $exp_robo = Expediente::select('expediente.id_solicitud')->distinct()->join('solicitud','solicitud.id','=','expediente.id_solicitud')->where('solicitud.perdida','=','robo')->whereIn('expediente.id_solicitud',$total_solicitud)->where('id_documento','=',$doc->id)->count();
                    $data_expediente[$key2] = ['texto'=>$doc->nombre,'cantidad'=>$expediente,'id_documento'=>$doc->id,'robo'=>$exp_robo,'danio'=>$exp_danio];   
                }
                $data[$key] = $data_expediente;
            }else{
                $row = Historico::where('estado','=',$key)->whereIn('id_solicitud',$total_solicitud)->distinct('id_solicitud')->count();
                $danio = Historico::where('historico.estado','=',$key)->join('solicitud','solicitud.id','=','historico.id_solicitud')->where('solicitud.perdida','=','danio')->whereIn('historico.id_solicitud',$total_solicitud)->distinct('historico.id_solicitud')->count();
                $robo = Historico::where('historico.estado','=',$key)->join('solicitud','solicitud.id','=','historico.id_solicitud')->where('solicitud.perdida','=','robo')->whereIn('historico.id_solicitud',$total_solicitud)->distinct('historico.id_solicitud')->count();
                $data[$key] = ['texto'=>$item,'cantidad'=>$row,'robo'=>$robo,'danio'=>$danio];
            }
            
         }
        
        return json_encode(['data' => $data]);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function dashboard_dos(Request $request)
    {
        $input = $request->all();

        $inicio = isset($input['inicio']) ? $input['inicio'] : null;
        $fin = isset($input['fin']) ? $input['fin'] : null;
        $danio = isset($input['danio']) ? true : false;
        $robo = isset($input['robo']) ? true : false;

        
        return view('pages.dashboard_graficas_dos',[
            'inicio' => $inicio,
            'fin' => $fin,
            'danio' => $danio,
            'robo' => $robo,
        ]);
    }
    public function dashboard_dos_ajax($inicio = null, $fin = null, $danio = null,$robo = null)
    {
        $model = new Solicitud();
        
        $header = $model->const_estados();

        $data = [];
        foreach($header as $key => $item){
            if($key != 'carga_documentos'){
                $solicitud = Solicitud::select('id','updated_at',DB::raw("(select TIMESTAMPDIFF(DAY,updated_at,NOW()) from historico where historico.id_solicitud =solicitud.id AND  historico.estado=solicitud.estado limit 1) as dias"))->where('estado','=',$key)->where(function($q) use ($inicio,$fin){
                    if($inicio != null){
                        $q->where('updated_at','>=',$inicio);
                    }
                    if($fin != null){
                        $q->where('updated_at','<=',$fin);
                    }
                })->distinct('id')->get();
            }else{
                $solicitud = Solicitud::select('id','updated_at',DB::raw("(select TIMESTAMPDIFF(DAY,updated_at,NOW()) from historico where historico.id_solicitud =solicitud.id AND  historico.estado=solicitud.estado limit 1) as dias"))->whereIn('estado',['carga_documentos','por_tramitar'])->where(function($q) use ($inicio,$fin){
                    if($inicio != null){
                        $q->where('updated_at','>=',$inicio);
                    }
                    if($fin != null){
                        $q->where('updated_at','<=',$fin);
                    }
                })->distinct('id')->get();
            }
            
            $uno = $solicitud->where('dias','<','2')->count();
            $dos = $solicitud->where('dias','=','2')->count();
            $tres = $solicitud->where('dias','>','2')->count();
            $data[$key] = ['texto' => $item, 'cantidad' => $solicitud->count(),'uno' =>$uno,'dos' => $dos,'tres' => $tres];

        }
        return json_encode(['data' => $data]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function dashboard_dos_detalle($texto,$doc,$dias)
    {
        $data = Solicitud::join('persona','persona.id','=','solicitud.id_persona')
                    ->select('solicitud.numero','vehiculo.placa','persona.nombres','persona.apellidos','persona.razon_social','solicitud.updated_at',DB::raw('DATEDIFF(now(),solicitud.updated_at) as dias'))
                    ->leftjoin('vehiculo','vehiculo.id','=','solicitud.id_vehiculo')
                    //->where('solicitud.estado','=',$doc)
                    ->where(function($q) use ($doc){
                        if($doc != 'carga_documentos'){
                            $q->where('solicitud.estado','=',$doc);
                        }else{
                            $q->whereIn('solicitud.estado',['carga_documentos','por_tramitar']);
                        }
                    })
                    ->whereRaw('DATEDIFF(now(),solicitud.updated_at) >?',$dias)
                    ->get();

        return view('pages.reportes.detalle_dos',[
            'data' => $data,
            'texto' => $texto
        ]);
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
