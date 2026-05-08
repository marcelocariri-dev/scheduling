<?php

namespace App\Repository;

use App\FIlters\UserFilter;
use App\Models\User;

class UserRepository
{
    private User $model;

    public function __construct()
    {
        $this->model = new User();
    }

    public function filterPaginated(UserFilter $filters, int $perpage)
    {
        return $this->model->with('agendamentos')
            ->filter($filters)
            ->orderBy('name', 'asc')
            ->paginate($perpage);
    }

    public function getId(int $id): ?User
    {
        return $this->model->with('agendamentos')->find($id);
    }

    public function salvar(array $dados): User
    {
        return $this->model->updateOrCreate(['id' => $dados['id'] ?? null], $dados);
    }

    public function destroyId(int $id): bool
    {
        $user = $this->model->findOrFail($id);

        if ($user->agendamentos()->count() > 0) {
            throw new \Exception('Existem agendamentos ligados a esse usuário.');
        }

        $user->delete();
        return true;
    }
}
