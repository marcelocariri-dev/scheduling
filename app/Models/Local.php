<?php

namespace App\Models;

use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Model;

class Local extends Model
{   use Filterable;

    protected $table = "locais";
    protected $fillable = [
        'nome',
        'descricao',
        'ativo',


    ];


    protected $casts = [
        'ativo' => 'boolean',

    ];

    // Relacionamentos
    public function agendamentos(){
        return $this->hasMany(Agendamento::class);
    }
}
