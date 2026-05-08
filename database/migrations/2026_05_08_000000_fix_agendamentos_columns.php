<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('agendamentos', function (Blueprint $table) {
            $table->string('status')->nullable()->default('confirmado')->change();
            $table->string('observacoes')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('agendamentos', function (Blueprint $table) {
            $table->boolean('status')->nullable()->default(false)->change();
            $table->string('observacoes')->nullable(false)->change();
        });
    }
};
