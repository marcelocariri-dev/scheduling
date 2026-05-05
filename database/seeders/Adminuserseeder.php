<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Enums\UserRole;
use App\Models\User;

class AdminUserseeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'fabrica@softcomtecnologia.com.br'],
            [
                'name'     => 'Administrador',
                'password' => bcrypt('password'),
                'tipo'     => UserRole::ADMIN->value,
            ]
        );

        $admin->assignRole(UserRole::ADMIN->value);

        // Gestor de exemplo
      //  $gestor = User::firstOrCreate(
          //  ['email' => 'gestor@empresa.com'],
          //  [
             //   'name'     => 'Gestor RH',
              //  'password' => bcrypt('password'),
              //  'setor'    => 'RH',
             //   'tipo'     => UserRole::GESTOR->value,
            //]
       // );

     //   $gestor->assignRole(UserRole::GESTOR->value);

        // Funcionário de exemplo
        $funcionario = User::firstOrCreate(
            ['email' => 'funcionario@softcomtecnologia.com'],
            [
                'name'     => 'SoftcomFuncionario',
                'password' => bcrypt('password'),
                'tipo'     => UserRole::FUNCIONARIO->value,
            ]
        );

        $funcionario->assignRole(UserRole::FUNCIONARIO->value);
    }
}
