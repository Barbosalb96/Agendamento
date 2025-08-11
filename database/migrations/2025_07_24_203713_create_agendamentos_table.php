<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
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
            $table->string('cpf', 11)->nullable();
            $table->string('documento', 11)->nullable();
            $table->string('rg', 20)->nullable();
            $table->string('telefone', 14);
            $table->enum('nacionalidade', ['brasileiro', 'estrangeiro']);
            $table->enum('nacionalidade_grupo', ['brasileiro', 'estrangeiro']);
            $table->boolean('deficiencia')->default(false);
            $table->date('data');
            $table->time('horario');
            $table->boolean('grupo')->default(false);
            $table->enum('status', ['Verde', 'Vermelho', 'Laranja'])->nullable();
            $table->text('observacao')->nullable();
            $table->integer('quantidade')->default(1);
            $table->time('horario_comparecimento')->nullable();
            $table->time('horario_entrada')->nullable();
            $table->foreignIdFor(User::class, "autorizador")->nullable()->constrained('users');
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
