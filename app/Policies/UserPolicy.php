<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function before(User $user): ?bool{
        if($user->hasRole('admin')){
            return true;
        }
        return null;

    }

    public function viewany (User $user): bool  {
        return  $user->hasPermissionTo('usuario.listar');
    }

    public function view(User $user, User $model): bool
    {
        // Qualquer um pode ver seu próprio perfil
        return $user->id === $model->id;
    }


    public function create(User $user): bool
    {
        return $user->hasPermissionTo('usuarios.criar');
    }

    public function update(User $user, User $model): bool
    {
        // Pode editar o próprio perfil
        if ($user->id === $model->id) {
            return true;
        }

        return $user->hasPermissionTo('usuarios.editar');
    }

    public function delete(User $user, User $model): bool
    {
        // Ninguém deleta a si mesmo (tratado também no controller)
        if ($user->id === $model->id) {
            return false;
        }

        return $user->hasPermissionTo('usuarios.deletar');
    }



}
