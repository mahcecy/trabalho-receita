<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alimentos', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->decimal('quantidade', 8, 2);
            $table->string('unidade', 10);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alimentos');
    }
};
