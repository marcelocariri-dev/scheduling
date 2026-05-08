<?php

namespace App\Http\Controllers;

use App\FIlters\AgendamentoFIlter;
use App\Http\Requests\AgendamentoRequest;
use App\Http\Resources\AgendamentoResource;
use App\Repository\AgendamentosRepository;


use App\Repository\UserRepository;
use Illuminate\Http\Request;
use App\Models\Agendamento;
use Illuminate\Http\JsonResponse;


class AgendamentoApiController extends Controller
{
    private $repository;

    public function __construct(AgendamentosRepository $repository, UserRepository $user)
    {
        $this->repository = $repository;

    }

    //GET /api/eventos - Listar todos os eventos - INDEX => convenção
    public function index(Request $request)
    {
        $this->authorize('viewAny', Agendamento::class);
        try {

            $filter = new AgendamentoFIlter($request);
            $perpage = $request->input('perpage', 15);
            $getAgendamento = $this->repository->filterPaginated($filter, $perpage);
            return AgendamentoResource::collection($getAgendamento);
        } catch (\Exception $ex) {
            return response()->json([
                'message' => 'erro ao listar local',
                'erro' => $ex->getMessage()
            ], 500);
        }
    }


    public function store(AgendamentoRequest $request): JsonResponse
    {
        $this->authorize('create', Agendamento::class);
        try {

            $dados = $request->validated();
            $dados['status'] = $dados['status'] ?? 'confirmado';
            $salvar = $this->repository->salvar($dados);
            return response()->json([
                'message' =>  'local criado com sucesso',
                'data' => new AgendamentoResource($salvar)
            ], 201);
        } catch (\Exception $ex) {
            return response()->json(
                [
                    'message' => 'erro ao criar local',
                    'erro' => $ex->getMessage()
                ],
                500
            );
        }
    }

    /**
     * Display the resource.
     */
    public function show($id): JsonResponse
    {
        try {
            $get =  $this->repository->getId($id);
            $this->authorize('view', $get );
            return response()->json([new AgendamentoResource($get)]);
        } catch (\Exception $ex) {
            return response()
                ->json([
                    'erro' => $ex->getMessage()
                ], 404);
        }
    }


    public function update(AgendamentoRequest $request, $id): JsonResponse
    {
        $id = $this->repository->getId($id);
        if (!$id) {
            return response()->json([
                "message" => 'o id não pôde ser localizado'
            ], 404);
        }
        $this->authorize('update', $id);
        try {

            //array merge pega o array que está no request e junta id => $id fazendo um array só
            $local =  array_merge($request->validated(), ['id' => $id]);

            $update = $this->repository->salvar($local);
            return response()->json([
                "message" => 'evento atualizado com sucesso',
                'data' => new AgendamentoResource($update)
            ], 200);
        } catch (\Exception $ex) {
            return response()->json([
                "erro" => $ex->getMessage()
            ], 500);
        }
    }


    public function destroy($id)
    {


        $agendamentoId = $this->repository->getId($id);
        if (!$agendamentoId) {
            return response()->json([
                "message" => 'usuario não encontrado'
            ], 404);
        }
        $this->authorize('delete', $agendamentoId);
        try {

            $this->repository->destroyId($agendamentoId);
            return response()->json([
                'message' => 'local deletado com sucesso',

            ], 200);
        } catch (\Exception $ex) {

            return response()->json([
                'message' => 'não foi possível deletar o registro',
                'erro' => $ex->getMessage(),

            ], 500);
        }
    }
}
