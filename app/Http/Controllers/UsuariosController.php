<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use App\Models\Notaria;
use App\Models\Persona;
use App\Models\User;
use Alert;
use DB;

class UsuariosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $data = User::where('id','!=',auth()->user()->id)->get();
        
        return view('pages.usuarios.lista',[
            'data'=>$data,
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
        $roles = Role::pluck('name','id');
        $notarias = Notaria::pluck('nombre','id');
        return view('pages.usuarios.form',[
            'roles'=>$roles,
            'notarias'=>$notarias
        ]);
    }
    public function store(Request $request)
    {
        $input = $request->all();
        
        Validator::make($input, [
            'nombres' => ['required', 'string', 'max:255'],
            'email' =>  ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'rol' => ['required']
        ])->validate();

        $rol = Role::find($input['rol']);

        $user = User::create([
            'name' => $input['nombres'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
        ]);

        $user->assignRole($rol->name);

        Persona::create([
            'nombres'=>$input['nombres'],
            'apellidos'=>$input['apellidos'],
            'correo'=>$input['email'],
            'id_usaurio'=>$user->id,
            'id_notaria'=>$input['notaria']
        ]);

        Alert::toast('Usuario registrado correctamente.','success');
        return redirect()->route('usuario.index');    
    }

    public function edit($id)
    {
        $edit = User::find($id);
        $roles = Role::pluck('name','id');
        $notarias = Notaria::pluck('nombre','id');
        return view('pages.usuarios.form',[
            'roles'=>$roles,
            'notarias'=>$notarias,
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
        $user = User::find($id);
        //dd($user->getpersona);
        if($user->getpersona != null){
            Validator::make($input, [
                'nombres' => ['required', 'string', 'max:255'],
                'email' =>  ['required', 'string', 'email', 'max:255'],
                'rol' => ['required']
            ])->validate();
        }
        if($user->getpersona != null)
            $user->name = $input['nombres'];
        $user->email = $input['email'];
        $user->save();
        //dd($input);
        if($user->getpersona != null){
            $persona = Persona::where('id_usaurio',$id)->first();
            $persona->nombres = $input['nombres'];
            $persona->apellidos = $input['apellidos'];
            $persona->correo = $input['email'];
            $persona->id_notaria = $input['notaria'];
            $persona->save();
        }
        

        DB::table('model_has_roles')->where([
            ['role_id','=',$user->getrol_user->role_id],
            ['model_id','=',$user->getrol_user->model_id]
        ])->update([
            'role_id'=>$input['rol'],
        ]);

        Alert::toast('Usuario actualizado correctamente.','success');
        return redirect()->route('usuario.index'); 
    }
    public function update_clave(Request $request)
    {
        $input = $request->all();

        Validator::make($input, [
            'clave' => ['required', 'string', 'min:8'],
        ])->validate();

        DB::table('users')->where([
            ['id','=',$input['id']]
        ])->update([
            'password' => Hash::make($input['clave'])
        ]);

        return json_encode(true);
    }
}
