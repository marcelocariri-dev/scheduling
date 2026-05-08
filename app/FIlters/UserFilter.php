<?php

namespace App\FIlters;

class UserFilter extends QueryFilter
{
    /**
     * Create a new class instance.
     */
    public function __construct(\Illuminate\Http\Request $request)
    {
        parent::__construct($request);
    }

    public function nome(string $value): void {
        $this->builder->where('name', 'like', "%{$value}%");
    }

    public function tipo(string $value): void {
        $this->builder->where('tipo', 'like', "%{$value}%");
    }

}
