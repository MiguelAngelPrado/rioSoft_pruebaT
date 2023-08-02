<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Deposito;
use App\Models\RecepcionVehiculo;
use Alert;
use DB;

class DepositoController extends Controller
{
    public function getEstado($index= null){
        $data = [
                    1 => 'Activo',
                    2 => 'Inactivo'
                ];
        return $index != null ? $data[$index] : $data;
    }

    public function index()
    {
        //
        $data = Deposito::all();
        $controlador = new DepositoController();
        return view('pages.deposito.lista',[
            'data'=>$data,
            'controlador'=>$controlador
        ]);
    }

    public function index_envio(){
        $data = RecepcionVehiculo::where('estado','=',1)->get();
        
        return view('pages.deposito.envio',[
            'data' => $data,
        ]);
    }

    public function index_recepcion(){
        $data = RecepcionVehiculo::whereIn('estado',[1,2])->get();
        
        return view('pages.deposito.recepcion',[
            'data' => $data,
        ]);
    }

    public function lista_vehiculos(Request $request){
        $input = $request->all();
        if (isset($input['search'])) {
            $keyword = strtolower($input['search']);

            $prm =str_replace(' ','%',$keyword);

            try{
                $vehiculos = DB::table('vehiculo')
                                ->select('vehiculo.id','vehiculo.placa','persona.apellidos','persona.nombres','inv_deposito.nombre as deposito')
                                ->join('solicitud','solicitud.id_vehiculo','=','vehiculo.id')
                                ->join('persona','persona.id','=','solicitud.id_persona')
                                ->join('inv_deposito','inv_deposito.id','=','solicitud.id_deposito')
                                ->whereNotNull('solicitud.id_deposito')
                                ->whereRaw("CONCAT_WS(vehiculo.placa,' ',persona.apellidos,' ',persona.nombres) like ?",'%'.$prm.'%')
                                ->get();
                //dd($vehiculos);
                $data = [];
                foreach ($vehiculos as $id => $item) {
                    $texto = '( Placa : '.$item->placa. ') Responsable :'. $item->apellidos . ' ' . $item->nombres.' Deposito: '.$item->deposito;
                    $data[] = ['id' => $item->id, 'text' => ($texto)];
                }

                return \Response::json(array_slice($data, 0, 1000));
            }
            catch(Exception $e){
              return \Response::json( "error" );

            }
        }else
            return \Response::json(null);
    }

    public function lista_depositos(Request $request){
        $input = $request->all();
        if (isset($input['search'])) {
            $keyword = strtolower($input['search']);

            $prm =str_replace(' ','%',$keyword);

            try{
                $depositos = DB::table('inv_deposito')
                                ->whereRaw("nombre like ?",'%'.$prm.'%')
                                ->get();

                $data = [];

                foreach ($depositos as $id => $item) {
                    $data[] = ['id' => $item->id, 'text' => $item->nombre];
                }

                return \Response::json(array_slice($data, 0, 1000));
            }
            catch(Exception $e){
              return \Response::json( "error" );

            }
        }else
            return \Response::json(null);
    }

    public function getNumTranferencia(){
        $numero = 1;
        $item_res = DB::table('transferencia_vehiculo')
                    ->orderBy('num_tranferencia','desc')
                    ->first();

        if($item_res != null){
            $numero = ($item_res->num_tranferencia+1);
        }
        return $numero;
    }
    public function num_transferencia_depositos(Request $request){
        $input = $request->all();
        $res = 1;
        if($input['opcion'] == 'nro_transaccion'){
            $res = $this->getNumTranferencia();
        }
        return json_encode(['success' => true , 'numero' => str_pad($res,10,'0',STR_PAD_LEFT)]);
    }
    public function save_recepcion(Request $request){
        $input = $request->all();

        $item_edit = RecepcionVehiculo::find($input['modal_id_movimiento']);
        $item_edit->comentario_recibir = $input['motivo'];
        $item_edit->user_id_recibir = auth()->user()->id;
        $item_edit->fecha_recibir = date('Y-m-d H:i:s');
        $item_edit->estado = 2;
        $item_edit->save();

        Alert::toast('Vehiculo recibido','success');
        return redirect()->route('deposito_recepcion.index');
    }
    public function save_salidas(Request $request){
        $input = $request->all();
        $texto = '';

        if($input['modal_id_movimiento'] != 0){
            $item_edit = RecepcionVehiculo::find($input['modal_id_movimiento']);
            $item_edit->id_vehiculo = $input['vehiculo'];
            $item_edit->id_deposito_recibir = $input['deposito'];
            $item_edit->comentario_salida = $input['motivo'];
            $item_edit->save();
            $texto = 'Salida aactualizada correctamente';
        }else{
            $num_tranferencia = $this->getNumTranferencia();
        
            $data_solicitud = DB::table('solicitud')
                                ->where([
                                    ['id_vehiculo','=',$input['vehiculo']],
                                ])->first();

            $data_item = new RecepcionVehiculo();
            $data_item->num_tranferencia = $num_tranferencia;
            $data_item->id_deposito_salida = $data_solicitud->id_deposito;
            $data_item->id_vehiculo = $input['vehiculo'];
            $data_item->id_deposito_recibir = $input['deposito'];
            $data_item->comentario_salida = $input['motivo'];
            $data_item->user_id_salida = auth()->user()->id;
            $data_item->fecha_salida = date('Y-m-d H:i:s');
            $data_item->save();

            $texto = 'Salida registrada correctamente, Num transferencia ('.str_pad($data_item->num_tranferencia,10,'0',STR_PAD_LEFT).').';
        }
        
        Alert::toast($texto,'success');
        return redirect()->route('deposito_envio.index');
    }
    public function edit_envio($id){
        $item_edit = RecepcionVehiculo::find($id);

        $item_deposito = Deposito::where('id','=',$item_edit->id_deposito_recibir)->first();
        $item_vehiculo = DB::table('vehiculo')
                                ->select('vehiculo.id','vehiculo.placa','persona.apellidos','persona.nombres','inv_deposito.nombre as deposito')
                                ->join('solicitud','solicitud.id_vehiculo','=','vehiculo.id')
                                ->join('persona','persona.id','=','solicitud.id_persona')
                                ->join('inv_deposito','inv_deposito.id','=','solicitud.id_deposito')
                                ->where('vehiculo.id','=',$item_edit->id_vehiculo)
                                ->first();
//        dd($item_deposito);
        $data = [
            'num_tranferencia' => str_pad($item_edit->num_tranferencia,10,'0',STR_PAD_LEFT),
            'motivo' => $item_edit->comentario_salida,
            'deposito_recibir' => [
                                    'id' => $item_deposito->id,
                                    'nombre' => $item_deposito->nombre
                                    ],
            'vehiculo' =>[
                            'id' => $item_edit->id_vehiculo ,
                            'texto' => '( Placa : '.$item_vehiculo->placa. ') Responsable :'. $item_vehiculo->apellidos . ' ' . $item_vehiculo->nombres.' Deposito: '.$item_vehiculo->deposito
                        ]
        ];
        return json_encode(['success'=> true, 'data' => $data]);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('pages.deposito.form',[
            'estado'=>$this->getEstado()
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
            'nombre' => ['required', 'string', 'max:25','unique:inv_deposito,nombre'],
            'email' => ['email'],
            'estado' => ['required', 'integer'],
        ])->validate();
        
        Deposito::create([
            'nombre'=>$input['nombre'],
            'contact_name'=>$input['nombre_contacto'],
            'contact_telef'=>$input['telefono'],
            'enabled'=>$input['estado'],
            'address'=>$input['direccion'],
            'provincia'=>$input['provincia'],
            'email'=>$input['email'],
        ]);

        Alert::toast('Deposito registrado correctamente.','success');
        return redirect()->route('deposito.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deposito_reporte()
    {
        $data = DB::table('solicitud')
                    ->select(DB::raw("IF(inv_deposito.nombre IS NULL,'No asignado',inv_deposito.nombre) AS Deposito"),DB::raw("COUNT(solicitud.id) AS Cantidad"))
                    ->leftjoin('inv_deposito','inv_deposito.id','=','solicitud.id_deposito')
                    ->groupBy('inv_deposito.nombre')
                    ->get();

        return view('pages.deposito.reporte',['data' => $data]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $edit = Deposito::find($id);
        return view('pages.deposito.form',[
            'edit'=>$edit,
            'estado'=>$this->getEstado()
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

        Validator::make($input, [
            'nombre' => ['required', 'string', 'max:25'],
            'email' => ['email'],
            'estado' => ['required', 'integer'],
        ])->validate();


        $data = Deposito::find($id);
        $data->nombre= $input['nombre'];
        $data->contact_name= $input['nombre_contacto'];
        $data->contact_telef= $input['telefono'];
        $data->enabled= $input['estado'];
        $data->address= $input['direccion'];
        $data->provincia= $input['provincia'];
        $data->email= $input['email'];
        $data->save();

        Alert::toast('Deposito actuaizado correctamente.','success');
        return redirect()->route('deposito.index');        
    }

    public function ajax_salidas(Request $request){
        $input = $request->all();
        switch($input['opcion']){
            case 'anular_salida':
                $item_edit = RecepcionVehiculo::find($input['id']);
                $item_edit->estado = 3;
                $item_edit->save();
                return json_encode(['success'=>true]);
            break;
        }
        return json_encode($input);
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
