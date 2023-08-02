<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\TipoDoc;
use App\Models\Notaria;

class Persona extends Model
{
    use HasFactory;

    protected $table = 'persona';
    protected $fillable = [
        'nombres',
        'apellidos',
        'id_documento',
        'documento',
        'correo',
        'telefono',
        'tipo',
        'razon_social',
        'nro_ruc',
        'id_usaurio',
        'id_notaria'
    ];

    public function tipo_doc()
    {
        return $this->hasOne(TipoDoc::class,'id','id_documento');
    }
    public function getnotaria()
    {
        return $this->hasOne(Notaria::class,'id','id_notaria');
    }
}
