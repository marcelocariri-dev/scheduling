<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;




class AuthApiController extends Controller
{
    public function Register (RegisterRequest $request): JsonResponse{
       $dados = $request->validated();


       $register = User::create([
            'name' => $dados['name'],
            'email' => $dados['email'],
            'password' => Hash::make($dados['password']),
            'tipo' => UserRole::FUNCIONARIO->value

        ]);

        $register->assignRole(UserRole::FUNCIONARIO->value);
$token = $register->createToken('auth_token')->plainTextToken;
return response()->json([
'token' => $token,
'token_type'   => 'Bearer',
'user'         => new UserResource($register),
], 201);
    }


public function Login (LoginRequest $loginRequest): JsonResponse{
$dados = $loginRequest->validated();

$user = User::where('email', $dados['email'])->first();
if(!$user || !Hash::check($dados['password'], $user->password)){
    return response()->json(['message' => 'credenciais inválidas'], 401);
}

$token = $user->createToken('auth_token')->plainTextToken;

return response()->json([
    'access_token' => $token,
    'token_type'   => 'Bearer',
    'user'         => new UserResource($user),
    'roles'        => $user->getRoleNames(),
    'permissions'  => $user->getAllPermissions()->pluck('name'),
]);



}

public function logout(Request $request): JsonResponse
{
    /** @var \Laravel\Sanctum\PersonalAccessToken $token */
    $request->user()->currentAccessToken()->delete();
    return response()->json(['message' => 'logout realizado com sucesso']);
}
public function me (Request $request){
    $user = $request->user();
    return response()->json(['user' => new UserResource($user),
    'roles' => $user->getRoleNames(),
    'permission'  => $user->getAllPermissions()->pluck('name'),
],);


}

}
