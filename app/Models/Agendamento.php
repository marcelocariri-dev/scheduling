<?php

namespace App\Models;

use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Model;

class Agendamento extends Model
{ use Filterable;

    protected $table = "agendamentos";
    protected $fillable = [
        'user_id',
        'local_id',
        'titulo',
        'data',
        'hora_inicio',
        'hora_final',
        'observacoes',
        'status'


    ];

    //relacionamentos
    // se o nome do metodo é local o laravel procura por local_id

    public function local(){
       return $this->belongsTo(Local::class);
    }
//mesma coisa no user
    public function User(){
       return  $this->belongsTo(User::class);
     }

}
