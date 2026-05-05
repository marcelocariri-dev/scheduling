<?php

namespace App\Repository;

use App\FIlters\AgendamentoFIlter;
use App\Models\Agendamento;


class AgendamentosRepository {
    private $model;
    public function __construct()
    {
        $this->model = new Agendamento();
    }
public function filterPaginated(AgendamentoFIlter $filters, int $perpag)
{
    return $this->model->with(['local'])
    ->filter($filters)
    ->orderBy('titulo', 'asc')
    ->paginate($perpag);

}

public function getId(int $id){
    return $this->model->with('local')->find($id);

}

public function salvar($dados){
    return $this->model->updateOrCreate(
        ['id' => $dados['id'] ?? null],
         $dados
    );
}

public function destroyId($id){

    return $this->model->destroy($id);
}


}
