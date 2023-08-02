<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
Use Alert;
use DB;

class RolesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Role::all();
        return view('pages.roles.listar',[
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
        $input = $request->all();

        DB::table('role_has_permissions')->where('role_id',$input['id_rol'])->delete();

        foreach($input['permiso_data'] as $item){
            DB::table('role_has_permissions')->insert([
                'permission_id'=>$item,
                'role_id'=>$input['id_rol'],
            ]);
        }

        Alert::toast('Rol actualizado correctamente.','success');
        return redirect()->route('roles.index');
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
        $edit = Role::find($id);
        $data = Permission::all();
        $permisos = DB::table('role_has_permissions')
                        ->select('permission_id')
                        ->where('role_id',$id)
                        ->pluck('permission_id')
                        ->toArray();
        return view('pages.roles.edit',[
            'edit'=>$edit,
            'data'=>$data,
            'permisos'=>$permisos
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
