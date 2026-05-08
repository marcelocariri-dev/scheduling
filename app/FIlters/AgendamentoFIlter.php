<?php

namespace App\FIlters;

class AgendamentoFIlter extends QueryFilter
{
    /**
     * Create a new class instance.
     */
    public function __construct(\Illuminate\Http\Request $request)
    {
        parent::__construct($request);
    }

    public function userid(string $value): void {
        $this->builder->where('user_id', $value);
    }

    public function titulo(string $value): void {
        $this->builder->where('titulo', 'like', "%{$value}%");
    }

    public function data(string $value): void {
        $this->builder->whereDate('data', $value);
    }

    public function status(string $value): void {
        $this->builder->where('status', $value);
    }

}
