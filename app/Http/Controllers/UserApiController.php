<?php

namespace App\Http\Controllers;

use App\FIlters\UserFilter;
use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Repository\UserRepository;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;



class UserApiController extends Controller

{
    private $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

public function index (Request $request){
$this->authorize('viewany', User::class);
    try{

        $filters = new UserFilter($request);
        $perpage = $request->input('perpage', 15);
        $listar = $this->repository->filterpaginated($filters, $perpage);
        return  UserResource::collection($listar);
    } catch( \Exception $ex){
        return response()
->json(['erro' => $ex->getmessage()]
, 500) ;

}
}
public function store(UserRequest $request): JsonResponse
    {
        $this->authorize('create', User::class);

        try {
            $dados = $request->validated();
            $user = $this->repository->salvar($dados);

            // Atribui role ao usuário
            if (isset($dados['tipo'])) {
                $user->syncRoles([$dados['tipo']]);
            }

            return response()->json([
                'message' => 'Usuário criado com sucesso.',
                'data'    => new UserResource($user),
            ], 201);
        } catch (\Exception $ex) {
            return response()->json([
                'message' => 'Erro ao criar usuário.',
                'erro'    => $ex->getMessage(),
            ], 500);
        }
    }

    public function show(int $id): JsonResponse
    {
        $user = $this->repository->getId($id);

        if (!$user) {
            return response()->json(['message' => 'Usuário não encontrado.'], 404);
        }

        $this->authorize('view', $user);

        return response()->json(new UserResource($user));
    }

    public function update(UserRequest $request, int $id): JsonResponse
    {
        $user = $this->repository->getId($id);

        if (!$user) {
            return response()->json(['message' => 'Usuário não encontrado.'], 404);
        }

        $this->authorize('update', $user);

        try {
            $dados = array_merge($request->validated(), ['id' => $id]);
            $atualizado = $this->repository->salvar($dados);

            // Atualiza role se o tipo mudou
            if (isset($dados['tipo'])) {
                $atualizado->syncRoles([$dados['tipo']]);
            }

            return response()->json([
                'message' => 'Usuário atualizado com sucesso.',
                'data'    => new UserResource($atualizado),
            ]);
        } catch (\Exception $ex) {
            return response()->json([
                'message' => 'Erro ao atualizar usuário.',
                'erro'    => $ex->getMessage(),
            ], 500);
        }
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $user = $this->repository->getId($id);

        if (!$user) {
            return response()->json(['message' => 'Usuário não encontrado.'], 404);
        }

        $this->authorize('delete', $user);

        // Impede deletar a si mesmo
        if ($request->user()->id === $user->id) {
            return response()->json([
                'message' => 'Você não pode deletar sua própria conta.',
            ], 403);
        }

        try {
            $this->repository->destroyId($id);

            return response()->json(['message' => 'Usuário deletado com sucesso.']);
        } catch (\Exception $ex) {
            return response()->json([
                'message' => 'Não foi possível deletar o usuário.',
                'erro'    => $ex->getMessage(),
            ], 500);
        }
    }

}