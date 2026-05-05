<?php

namespace App\Policies;

use App\Models\Agendamento;
use App\Models\User;

class AgendamentoPolicy
{
public function before (User $user, string $ability): ?bool{
if($user->hasrole('admin')){
    return true;

}
return null;

}

public function viewany(User $user):bool{
    return $user->hasAnyPermission(['agendamentos.listar', 'agendamentos.listar_todos']);
}

public function view(User $user, Agendamento $agendamento):bool{
return $user->hasAnyPermission(['agendamentos.listar', 'agendamentos.listar_todos']);

}

public function create (User $user):bool{
    return $user->hasPermissionTo(['agendamentos.criar']);
}

public function update(User $user, Agendamento $agendamento ):bool{

    if(!$user->hasPermissionTo([' agendamentos.editar'])){
return false;
    }
    if($user->id === $agendamento->user_id  ){
        return true;
    }
    return false;
}

public function delete(User $user, Agendamento $agendamento){
    if(!$user->hasPermissionTo([' agendamentos.deletar'])){
        return false;
            }
            if($user->id === $agendamento->user_id  ){
                return true;
            }
            return false;
}

}
