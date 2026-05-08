<?php

namespace App\FIlters;

class LocalFilter extends QueryFilter
{
    /**
     * Create a new class instance.
     */
    public function __construct(\Illuminate\Http\Request $request)
    {
        parent::__construct($request);
    }

    public function nome(string $value): void {
        $this->builder->where('nome', 'like', "%{$value}%");
    }

    public function ativo(string $value): void {
        $this->builder->where('ativo', (bool)$value);
    }

    public function id(string $value): void {
        $this->builder->where('id', $value);
    }


}
