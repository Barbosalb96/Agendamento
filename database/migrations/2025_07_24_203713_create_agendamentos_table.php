<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('agendamentos', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('nome');
            $table->string('email');
            $table->string('cpf', 14);
            $table->string('rg', 20);
            $table->string('telefone', 11);
            $table->enum('nacionalidade', ['brasileiro', 'estrangeiro']);
            $table->enum('nacionalidade_grupo', ['brasileiro', 'estrangeiro']);
            $table->boolean('deficiencia')->default(false);
            $table->date('data');
            $table->time('horario');
            $table->boolean('grupo')->default(false);
            $table->text('observacao')->nullable();
            $table->integer('quantidade')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agendamentos');
    }
};
