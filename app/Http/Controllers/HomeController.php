<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;
use Artisan;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public $base_uri;
    public $client;

    public function __construct()
    {
        $this->base_uri = 'https://rickandmortyapi.com/api/';

        $this->client = new Client([
                    'base_uri' => $this->base_uri,
        ]); 
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        try{
            $input = $request->all();
            $pagina = isset($input['pagina']) ? $input['pagina'] : '1';
            $id_personaje = isset($input['prm_nombre']) ? $input['prm_nombre'] : null;
            
            

            if($id_personaje == null){
                $response = $this->client->request('GET','character?page='.$pagina,['verify' => false]);      
            }else{
                $response = $this->client->request('GET','character/'.$id_personaje,['verify' => false]);
            }
            
            
            $data = json_decode($response->getBody()->getContents());
            
            return view('home',['data' => $data,'pagina' => $pagina,'id_personaje' => $id_personaje]);

        }catch(Exception $e){
            dd($e->message());
        }
       
        
    }

    public function lst_nombres(Request $request){
        $input = $request->all();
        if (isset($input['search'])){
            $keyword = strtolower($input['search']);

            $pagina = 1;
            $items = [];
            try{
                do{
                    $response = $this->client->request('GET','character?page='.$pagina,['verify' => false]);
                    $data = json_decode($response->getBody()->getContents());

                    foreach($data->results as $row){
                        
                        if (strpos(strtolower($row->name), $keyword) !== false) {
                            $items[] = ['id' => $row->id, 'text' => $row->name];        
                        }
                        
                    }
                    
                    $pagina++;
                    
                    
                }while($pagina != $data->info->pages);
                

                return \Response::json(array_slice($items, 0, 1000));

            }catch(Exception $e){
              return \Response::json( "error" );

            }

        }else
            return \Response::json(null);
    }
    public function personaje_show($id){
        $response = $this->client->request('GET','character/'.$id,['verify' => false]);
        
        $data = json_decode($response->getBody()->getContents());
        //dd($data);
        return view('pages.personaje_show',['data' => $data]);
        dd($id);
    }
}
