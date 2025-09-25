<?php

namespace App\Http\Controllers;

use App\Models\Alimento;
use Illuminate\Http\Request;

class AlimentoController extends Controller
{
    // Listar todos os alimentos
    public function index()
    {
        return response()->json(Alimento::all(), 200);
    }

    // Cadastrar novo alimento
    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'quantidade' => 'required|integer|min:1',
        ]);

        $alimento = Alimento::create($request->all());

        return response()->json($alimento, 201);
    }

    // Atualizar alimento existente
    public function update(Request $request, $id)
    {
        $alimento = Alimento::findOrFail($id);

        $request->validate([
            'nome' => 'sometimes|string|max:255',
            'quantidade' => 'sometimes|integer|min:1',
        ]);

        $alimento->update($request->all());

        return response()->json($alimento, 200);
    }

    // Deletar alimento
    public function destroy($id)
    {
        $alimento = Alimento::findOrFail($id);
        $alimento->delete();

        return response()->json(['message' => 'Alimento removido com sucesso'], 200);
    }
}
