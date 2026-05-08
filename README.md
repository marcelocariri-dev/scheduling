# Agenda API

API REST para gerenciamento de agendamentos de espaços/locais, com controle de acesso por papéis e permissões.

**Stack:** Laravel 11 · Laravel Sanctum · Spatie Permission

---

## Sumário

- [Instalação](#instalação)
- [Autenticação](#autenticação)
- [Papéis e Permissões](#papéis-e-permissões)
- [Endpoints](#endpoints)
  - [Auth](#auth)
  - [Agendamentos](#agendamentos)
  - [Locais](#locais)
  - [Usuários](#usuários)
- [Filtros de Listagem](#filtros-de-listagem)
- [Estrutura do Projeto](#estrutura-do-projeto)

---

## Instalação

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed --class=RolesAndPermissionsSeeder
php artisan db:seed --class=AdminUserSeeder
```

---

## Autenticação

A API usa **Laravel Sanctum** com tokens Bearer.

Todas as rotas privadas exigem o header:

```
Authorization: Bearer {token}
```

O token é retornado no login e no registro.

---

## Papéis e Permissões

| Permissão               | Admin | Gestor | Funcionário |
|-------------------------|:-----:|:------:|:-----------:|
| agendamentos.listar     | ✅    | ✅     | ✅          |
| agendamentos.listar_todos | ✅  | ❌     | ❌          |
| agendamentos.criar      | ✅    | ✅     | ✅          |
| agendamentos.editar     | ✅    | ✅     | ✅ (próprios)|
| agendamentos.deletar    | ✅    | ✅     | ✅ (próprios)|
| locais.listar           | ✅    | ✅     | ✅          |
| locais.criar            | ✅    | ✅     | ❌          |
| locais.editar           | ✅    | ✅     | ❌          |
| locais.deletar          | ✅    | ❌     | ❌          |
| usuarios.listar         | ✅    | ❌     | ❌          |
| usuarios.criar          | ✅    | ❌     | ❌          |
| usuarios.editar         | ✅    | ❌     | ❌          |
| usuarios.deletar        | ✅    | ❌     | ❌          |

---

## Endpoints

Base URL: `/api`

---

### Auth

#### POST `/register`
Cria um novo usuário com papel **funcionário**.

**Body:**
```json
{
  "name": "João Silva",
  "email": "joao@email.com",
  "password": "senha123",
  "password_confirmation": "senha123"
}
```

**Resposta 201:**
```json
{
  "token": "1|abc...",
  "token_type": "Bearer",
  "user": { "id": 1, "name": "João Silva", "email": "joao@email.com", ... }
}
```

---

#### POST `/login`
Autentica um usuário existente.

**Body:**
```json
{
  "email": "joao@email.com",
  "password": "senha123"
}
```

**Resposta 200:**
```json
{
  "access_token": "1|abc...",
  "token_type": "Bearer",
  "user": { ... },
  "roles": ["funcionario"],
  "permissions": ["agendamentos.listar", "agendamentos.criar", ...]
}
```

**Resposta 401:** Credenciais inválidas.

---

#### POST `/logout` `🔒`
Revoga o token atual.

**Resposta 200:**
```json
{ "message": "logout realizado com sucesso" }
```

---

#### GET `/me` `🔒`
Retorna os dados do usuário autenticado.

**Resposta 200:**
```json
{
  "user": { "id": 1, "name": "João Silva", ... },
  "roles": ["funcionario"],
  "permission": ["agendamentos.listar", ...]
}
```

---

### Agendamentos

Todas as rotas exigem autenticação `🔒`.

#### GET `/agendamentos`
Lista agendamentos paginados. Suporta [filtros](#filtros-de-listagem).

**Query params:** `perpage` (padrão: 15), `titulo`, `data`, `status`, `userid`

**Resposta 200:** Lista paginada de agendamentos.

---

#### POST `/agendamentos`

**Body:**
```json
{
  "user_id": 1,
  "local_id": 2,
  "titulo": "Reunião de planejamento",
  "data": "2026-06-10",
  "hora_inicio": "09:00",
  "hora_final": "10:30",
  "observacoes": "Trazer relatórios",
  "status": "confirmado"
}
```

| Campo        | Tipo    | Obrigatório | Regras                          |
|--------------|---------|:-----------:|---------------------------------|
| user_id      | integer | ✅          | deve existir em `users`         |
| local_id     | integer | ✅          | deve existir em `locais`        |
| titulo       | string  | ✅          | máx. 255 caracteres             |
| data         | date    | ✅          | hoje ou futuro (`Y-m-d`)        |
| hora_inicio  | string  | ✅          | formato `HH:MM`                 |
| hora_final   | string  | ✅          | formato `HH:MM`, após hora_inicio |
| observacoes  | string  | ❌          | máx. 1000 caracteres            |
| status       | string  | ❌          | `confirmado` ou `cancelado` (padrão: `confirmado`) |

**Resposta 201:**
```json
{
  "message": "local criado com sucesso",
  "data": { "id": 1, "titulo": "Reunião de planejamento", ... }
}
```

---

#### GET `/agendamentos/{id}`
Retorna um agendamento pelo ID.

**Resposta 200:** Dados do agendamento com local vinculado.
**Resposta 404:** Agendamento não encontrado.

---

#### PUT `/agendamentos/{id}`
Atualiza um agendamento. Mesmo body do POST.

**Resposta 200:**
```json
{
  "message": "evento atualizado com sucesso",
  "data": { ... }
}
```

---

#### DELETE `/agendamentos/{id}`
Remove um agendamento.

**Resposta 200:**
```json
{ "message": "local deletado com sucesso" }
```

---

### Locais

Todas as rotas exigem autenticação `🔒`.

#### GET `/locais`
Lista locais paginados. Suporta [filtros](#filtros-de-listagem).

**Query params:** `perpage` (padrão: 15), `nome`, `ativo`, `id`

---

#### POST `/locais`

**Body:**
```json
{
  "nome": "Sala de Reuniões A",
  "descricao": "Capacidade para 10 pessoas",
  "ativo": true
}
```

| Campo    | Tipo    | Obrigatório | Regras             |
|----------|---------|:-----------:|--------------------|
| nome     | string  | ✅          | máx. 255 caracteres|
| descricao| string  | ❌          | máx. 500 caracteres|
| ativo    | boolean | ❌          | `true` ou `false`  |

**Resposta 201:**
```json
{
  "message": "local criado com sucesso",
  "data": { "id": 1, "nome": "Sala de Reuniões A", ... }
}
```

---

#### GET `/locais/{id}`
Retorna um local com seus agendamentos (ordenados por data desc).

---

#### PUT `/locais/{id}`
Atualiza um local. Mesmo body do POST.

---

#### DELETE `/locais/{id}`
Remove um local. Falha se houver agendamentos vinculados.

**Resposta 500:** `"existe agendamentos ligados a esse local"`

---

### Usuários

Todas as rotas exigem autenticação `🔒` e papel **admin**.

#### GET `/users`
Lista usuários paginados.

**Query params:** `perpage` (padrão: 15), `nome`, `tipo`

---

#### POST `/users`

**Body:**
```json
{
  "name": "Maria Souza",
  "email": "maria@email.com",
  "password": "senha123",
  "password_confirmation": "senha123",
  "tipo": "gestor"
}
```

| Campo    | Tipo   | Obrigatório | Regras                              |
|----------|--------|:-----------:|-------------------------------------|
| name     | string | ✅          | máx. 255 caracteres                 |
| email    | string | ✅          | e-mail válido, único                |
| password | string | ✅          | mín. 8 caracteres, com confirmação  |
| tipo     | string | ✅          | `admin`, `gestor` ou `funcionario`  |

---

#### GET `/users/{id}`
Retorna dados de um usuário com seus agendamentos.

---

#### PUT `/users/{id}`
Atualiza um usuário. `password` é opcional no update.

---

#### DELETE `/users/{id}`
Remove um usuário. Não é possível deletar a própria conta. Falha se houver agendamentos vinculados.

---

## Filtros de Listagem

Os filtros são passados como query params. Exemplo:

```
GET /api/agendamentos?titulo=reunião&status=confirmado&perpage=10
```

| Recurso      | Filtros disponíveis              |
|--------------|----------------------------------|
| agendamentos | `titulo`, `data`, `status`, `userid` |
| locais       | `nome`, `ativo`, `id`            |
| usuarios     | `nome`, `tipo`                   |

---

## Estrutura do Projeto

```
app/
├── Enums/
│   └── UserRole.php              # Enum: admin, gestor, funcionario
├── FIlters/
│   ├── QueryFilter.php           # Base abstrata dos filtros
│   ├── AgendamentoFIlter.php
│   ├── LocalFilter.php
│   └── UserFilter.php
├── Http/
│   ├── Controllers/
│   │   ├── AuthApiController.php
│   │   ├── AgendamentoApiController.php
│   │   ├── LocalApiController.php
│   │   └── UserApiController.php
│   ├── Requests/                 # Validação de entrada
│   └── Resources/                # Formatação de saída (JSON)
├── Models/
│   ├── User.php
│   ├── Agendamento.php
│   └── Local.php
├── Policies/                     # Autorização por papel/permissão
│   ├── AgendamentoPolicy.php
│   ├── LocalPolicy.php
│   └── UserPolicy.php
├── Repository/                   # Camada de acesso a dados
│   ├── AgendamentosRepository.php
│   ├── LocaisRepository.php
│   └── UserRepository.php
└── Traits/
    └── Filterable.php            # Scope `filter()` para os models
```
