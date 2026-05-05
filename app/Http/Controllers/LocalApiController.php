<?php

namespace App\Http\Controllers;

use App\FIlters\LocalFilter;
use App\Http\Requests\LocalRequest;
use App\Http\Resources\LocalResource;
use App\Models\Local;
use App\Repository\LocaisRepository;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class LocalApiController extends Controller

{
    private $repository;
    public function __construct(LocaisRepository $repository)
    {
        $this->repository = $repository;
    }

    //GET /api/eventos - Listar todos os eventos - INDEX => convenção
    public function index(Request $request)
    {
        $this->authorize('viewAny', Local::class);
        try {

            $filter = new LocalFilter($request);
            $perpage = $request->input('perpage', 15);
            $getlocal = $this->repository->filterPaginated($filter, $perpage);
            return LocalResource::collection($getlocal);
        } catch (\Exception $ex) {
            return response()->json([
                'message' => 'erro ao listar local',
                'erro' => $ex->getMessage()
            ], 500);
        }
    }


    public function store(LocalRequest $request):JsonResponse
    {
        $this->authorize('create', Local::class);
        try {

            $dados = $request->validated();
            $salvar = $this->repository->salvar($dados);
            return response()->json([
                'message' =>  'local criado com sucesso',
                'data' => new LocalResource($salvar)
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
    public function show($id)
    {
        $get =  $this->repository->getId($id);


        if (!$get) {
            return response()->json(['message' => 'Local não encontrado.'], 404);
        }
        $this->authorize('view', $get);
        try {

            return new LocalResource($get);
        } catch (\Exception $ex) {
            return response()
                ->json([
                    'erro' => $ex->getMessage()
                ], 404);
        }
    }

    /**
     * Update the resource in storage.
     */
    public function update(LocalRequest $request, $id)
    {
        $id = $this->repository->getId($id);

        if (!$id) {
            return response()->json([
                "message" => 'o id não pôde ser localizado'
            ], 404);
        }

        $this->authorize('update', $id);


        try {

#o array_merge concatena o array vindo formrequest com parametro que o nome de id = $id

            $local = array_merge($request->validated(), ['id' => $id]);
            $update = $this->repository->salvar($local);
            return response()->json([
                "message" => 'evento atualizado com sucesso',
                'data' => new LocalResource($update)
            ], 200);
        } catch (\Exception $ex) {
            return response()->json([
                "erro" => $ex->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the resource from storage.
     */
    public function destroy(int $id)
    {
        $deletar = $this->repository->getId($id);
        if (!$deletar) {
            return response()->json([
                "message" => 'o id não pôde ser localizado'
            ], 404);
        }


        $this->authorize('delete', $deletar);

        try {

            $this->repository->destroyId($id);
            return response()->noContent();
        } catch (\Exception $ex) {

            return response()->json([
                'message' => 'não foi possível deletar o registro',
                'erro' => $ex->getMessage(),

            ], 500);
        }
    }
}
